<?php

namespace App\View\Composers;

use App\Models\Post;
use Roots\Acorn\View\Composer;

class Person extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.post-types.content-single-person',
        'partials.post-types.content-person',
    ];


    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'person' => $this->person(),
            'assignments' => $this->assignments(),
        ];
    }

    /**
     * Retrieve the person object with formatted meta fields.
     */
    public function person()
    {
        $personId = get_the_ID(); // @phpstan-ignore-line
        
        if (!$personId) {
            return null;
        }

        return Post::with(['meta', 'party.meta'])
            ->find($personId)
            ?->format();
    }

    /**
     * Get active assignments for the person.
     */
    public function assignments()
    {
        $personId = get_the_ID(); // @phpstan-ignore-line
        
        if (!$personId) {
            return collect();
        }

        $person = Post::find($personId);
        
        if (!$person) {
            return collect();
        }

        return $person->activeAssignments;
    }
}