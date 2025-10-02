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
            ->with(['meta'])
            ->get()
            ->format();

        $groupLeaders = Post::persons()
            ->published()
            ->withMeta('person_group_leader', '1')
            ->with(['meta', 'party.meta'])
            ->get()
            ->format();

        $decisionAuthorities = DecisionAuthority::with(['board', 'typeTerm'])
            ->ongoing()
            ->orderBy('title')
            ->get();

        $groupedAuthorities = $decisionAuthorities->groupBy(function($authority) {
            return $authority->typeTerm ? $authority->typeTerm->name : __('Other', 'fmr');
        })->sortKeys();

        return view('homepage', compact('parties', 'groupLeaders', 'groupedAuthorities'));
    }

}
