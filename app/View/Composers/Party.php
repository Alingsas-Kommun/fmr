<?php

namespace App\View\Composers;

use App\Models\Post;
use Roots\Acorn\View\Composer;

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
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        $party = $this->party();
        
        if (!$party) {
            return [];
        }

        $activeMembers = $this->members(active: true);
        
        if ($activeMembers->isEmpty() && !is_user_logged_in()) {
            abort(404);
        }

        return [
            'party' => $party,
            'activeMembers' => $activeMembers,
            'inactiveMembers' => is_user_logged_in() ? $this->members(active:false) : collect(),
        ];
    }

    /**
     * Retrieve the party object with aggregated meta fields.
     */
    public function party()
    {
        $partyId = get_the_ID(); // @phpstan-ignore-line
        
        if (!$partyId) {
            return null;
        }

        return Post::with(['meta'])
            ->find($partyId)
            ?->format();
    }

    /**
     * Get all members for the party based on active status.
     */
    public function members($active = true)
    {
        $party = $this->party();
        
        if (!$party) {
            return collect();
        }

        $persons = Post::persons()
            ->published()
            ->withMeta('person_party', $party->id)
            ->with(['meta'])
            ->orderBy('post_title');

        if ($active) {
            $persons->activeAssignments();
        } else {
            $persons->inactiveAssignments();
        }

        return $persons->get()->format();
    }
}