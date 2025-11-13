<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\DecisionAuthority;

class HomeController extends Controller
{
    /**
     * Display the homepage
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $parties = Post::parties()
            ->published()
            ->withActiveMembers()
            ->orderBy('post_title')
            ->with(['meta'])
            ->get()
            ->format();

        $groupLeaders = Post::persons()
            ->published()
            ->withMeta('person_group_leader', '1')
            ->active()
            ->with(['meta', 'party.meta'])
            ->get()
            ->format();

        $decisionAuthorities = DecisionAuthority::with(['board.categoryTerm'])
            ->ongoing()
            ->orderBy('title')
            ->get();
        
        $grouped = $decisionAuthorities->groupBy(
            fn($authority) => $authority->board->categoryTerm->name ?? 'unsorted'
        );
        
        // Sort groups by term_order (nulls -> large number so they end up last)
        $sorted = $grouped->sortBy(
            fn($group, $categoryName) => (int) ($group->first()?->board?->categoryTerm?->term_order ?? 9999)
        );
        
        // Rebuild a collection keyed by category name (so keys appear in the new order)
        $groupedAuthorities = $sorted->mapWithKeys(fn($group, $name) => [$name => $group]);

        return view('homepage', compact('parties', 'groupLeaders', 'groupedAuthorities'));
    }

}
