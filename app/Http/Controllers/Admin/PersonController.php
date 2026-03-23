<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;

class PersonController
{
    /**
     * Get all active persons.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Post::persons()
            ->published()
            ->orderBy('post_title')
            ->get();
    }

    /**
     * Person ID => display label for select fields.
     *
     * @param  array<string, mixed>  $options
     *         name_format: 'post_title'|'full_name' — default 'post_title'
     *         include: list of 'ssn', 'party_shortening' (order controls label segments after the name)
     * @return array<int, string>
     */
    public function getSelectOptions(array $options = []): array
    {
        $nameFormat = $options['name_format'] ?? 'post_title';
        $include = $options['include'] ?? [];

        $with = [];
        if ($nameFormat === 'full_name' || in_array('ssn', $include, true)) {
            $with[] = 'meta';
        }
        
        if (in_array('party_shortening', $include, true)) {
            $with[] = 'party.meta';
        }

        $with = array_values(array_unique($with));

        $persons = Post::persons()
            ->published()
            ->orderBy('post_title')
            ->with($with)
            ->get();

        $result = [];

        foreach ($persons as $person) {
            $result[$person->ID] = $this->formatPersonSelectLabel($person, $nameFormat, $include);
        }

        return $result;
    }

    /**
     * @param  array<int, string>  $include
     */
    private function formatPersonSelectLabel(Post $person, string $nameFormat, array $include): string
    {
        if ($nameFormat === 'full_name') {
            $first = $this->personMetaValue($person, 'person_firstname') ?? '';
            $last = $this->personMetaValue($person, 'person_lastname') ?? '';
            $label = trim($first . ' ' . $last) ?: $person->post_title;
        } else {
            $label = $person->post_title;
        }

        foreach ($include as $part) {
            if ($part === 'ssn') {
                $ssn = $this->personMetaValue($person, 'person_ssn');

                if ($ssn !== null && $ssn !== '') {
                    $label .= ' (' . $ssn . ')';
                }
            } elseif ($part === 'party_shortening') {
                $shortening = $person->party?->meta
                    ->firstWhere('meta_key', 'party_shortening')
                    ?->meta_value;
                    
                if ($shortening !== null && $shortening !== '') {
                    $label .= ' (' . $shortening . ')';
                }
            }
        }

        return $label;
    }

    private function personMetaValue(Post $person, string $key): ?string
    {
        $value = $person->relationLoaded('meta')
            ? $person->meta->firstWhere('meta_key', $key)?->meta_value
            : $person->getMeta($key);

        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    /**
     * Check if a person has active assignments and is active.
     *
     * @param int $person_id
     * @return bool
     */
    public function isActive($person_id)
    {
        return Post::persons()
            ->where('ID', $person_id)
            ->published()
            ->active()
            ->exists();
    }
}
