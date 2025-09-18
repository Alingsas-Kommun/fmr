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

        $groupLeaders = Post::persons()
            ->published()
            ->withMeta('person_group_leader', '1')
            ->get();

        $boards = Post::boards()
            ->published()
            ->get();

        return view('homepage', compact('parties', 'groupLeaders', 'boards'));
    }
}
