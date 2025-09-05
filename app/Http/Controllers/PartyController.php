<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PartyController extends Controller
{
    /**
     * Display a listing of the parties.
     */
    public function index()
    {
        $parties = Post::parties()
            ->orderBy('post_title')
            ->get();

        return view('parties.index', compact('parties'));
    }

    /**
     * Display the specified party.
     */
    public function show($id)
    {
        $party = Post::parties()->findOrFail($id);

        return view('parties.show', compact('party'));
    }
}
