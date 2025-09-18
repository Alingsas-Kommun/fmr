<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;

class BoardController
{
    /**
     * Get all active boards.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Post::boards()
            ->published()
            ->orderBy('post_title')
            ->get();
    }
}
