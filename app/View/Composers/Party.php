<?php

namespace App\View\Composers;

use App\Models\Post;
use Roots\Acorn\View\Composer;
use Illuminate\Support\Str;

class Party extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.post-types.content-single-party',
        'partials.post-types.content-party',
    ];

    /**
     * List of meta fields to be passed via the party object.
     *
     * @var array
     */
    protected static $metaFields = [
        'party_description',
        'party_shortening',
        'party_group_leader',
        'party_address',
        'party_zip',
        'party_city',
        'party_website',
        'party_email',
        'party_phone',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'party' => $this->partyWithMeta(),
            'thumbnail' => $this->thumbnail(),
            'members' => $this->members(),
        ];
    }

    /**
     * Retrieve the party object.
     */
    public function party()
    {
        $partyId = get_the_ID(); // @phpstan-ignore-line
        
        if (!$partyId) {
            return null;
        }

        return Post::find($partyId);
    }

    /**
     * Retrieve the party object with aggregated meta fields.
     */
    public function partyWithMeta()
    {
        $party = $this->party();
        
        if (!$party) {
            return null;
        }

        $metaValues = $this->partyMeta();
        
        foreach ($metaValues as $key => $value) {
            $propertyName = Str::camel(Str::replace('party_', '', $key)); 
            $party->$propertyName = $value;
        }

        return $party;
    }

    /**
     * Get the thumbnail for the party.
     */
    public function thumbnail()
    {
        $party = $this->party();
        
        if (!$party) {
            return null;
        }

        return $party->thumbnail();
    }

    /**
     * Get all visible party meta fields in a single query.
     */
    public function partyMeta()
    {
        $party = $this->party();
        
        if (!$party) {
            return [];
        }

        return $party->getVisibleMetaValues(static::$metaFields);
    }

    /**
     * Get all members for the party.
     */
    public function members()
    {
        $party = $this->party();
        
        if (!$party) {
            return collect();
        }

        $persons = Post::persons()
            ->published()
            ->withMeta('person_party', $party->ID)
            ->orderBy('post_title')
            ->get();

        return $persons;
    }
}