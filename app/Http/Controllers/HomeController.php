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
            ->orderBy('post_title')
            ->get();

        $groupLeaders = Post::persons()
            ->whereHas('meta', function($query) {
                $query->where('meta_key', 'person_group_leader')
                      ->where('meta_value', '1');
            })
            ->get();

        $boards = Post::boards()
            ->get();

        return view('homepage', compact('parties', 'groupLeaders', 'boards'));
    }
}
