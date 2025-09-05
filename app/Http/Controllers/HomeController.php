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

        return view('homepage', compact('parties'));
    }
}
