<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;

class PartyController
{
    /**
     * Get all parties.
     *
     * @return \App\Collections\PostCollection
     */
    public function getAll()
    {
        return Post::parties()
            ->published()
            ->orderBy('post_title')
            ->get()
            ->format();
    }
}

