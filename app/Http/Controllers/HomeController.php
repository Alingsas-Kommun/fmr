<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        $parties = Post::parties()
            ->published()
            ->orderBy('post_title')
            ->get();

        $groupLeaders = $this->getGroupLeadersWithParties();

        $boards = Post::boards()
            ->published()
            ->get();

        return view('homepage', compact('parties', 'groupLeaders', 'boards'));
    }

    /**
     * Get group leaders with their party information
     */
    private function getGroupLeadersWithParties()
    {
        // Step 1: Get group leaders with their meta data eagerly loaded
        $groupLeaders = Post::persons()
            ->published()
            ->withMeta('person_group_leader', '1')
            ->with('meta')
            ->get();

        // Step 2: Extract unique party IDs from group leaders
        $partyIds = $groupLeaders
            ->map(function ($leader) {
                return $leader->getMeta('person_party');
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Step 3: Fetch all parties with their meta data in a single query
        $parties = collect();
        if (!empty($partyIds)) {
            $parties = Post::whereIn('ID', $partyIds)
                ->where('post_type', 'party')
                ->with('meta')
                ->get()
                ->keyBy('ID');
        }

        // Step 4: Map parties to group leaders
        $groupLeaders->each(function ($leader) use ($parties) {
            $partyId = $leader->getMeta('person_party');
            if ($partyId && $parties->has($partyId)) {
                $leader->party = $parties->get($partyId);
            }
        });

        return $groupLeaders;
    }
}
