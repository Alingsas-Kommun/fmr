<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;

class PersonController
{
    /**
     * Get all active persons.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Post::persons()
            ->orderBy('post_title')
            ->get();
    }
}
