<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\DecisionAuthority;

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

        $groupLeaders = Post::persons()
            ->published()
            ->withMeta('person_group_leader', '1')
            ->with('party')
            ->get();

        $decisionAuthorities = DecisionAuthority::with(['board', 'typeTerm'])
            ->where(function($query) {
                $query->where('start_date', '<=', now())
                    ->where(function($q) {
                        $q->where('end_date', '>=', now())
                            ->orWhereNull('end_date');
                    });
            })
            ->orderBy('title')
            ->get();

        $groupedAuthorities = $decisionAuthorities->groupBy(function($authority) {
            return $authority->typeTerm ? $authority->typeTerm->name : __('Other', 'fmr');
        })->sortKeys();

        return view('homepage', compact('parties', 'groupLeaders', 'groupedAuthorities'));
    }

}
