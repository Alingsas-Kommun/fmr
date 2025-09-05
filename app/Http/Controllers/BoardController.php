<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BoardController extends Controller
{
    /**
     * Display a listing of the boards.
     */
    public function index()
    {
        $boards = Post::boards()
            ->orderBy('post_title')
            ->get();

        return view('boards.index', compact('boards'));
    }

    /**
     * Display the specified board.
     */
    public function show($id)
    {
        $board = Post::boards()->findOrFail($id);
        $assignments = $board->activeAssignments;

        return view('boards.show', compact('board', 'assignments'));
    }
}
