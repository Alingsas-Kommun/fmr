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
            ->published()
            ->orderBy('post_title')
            ->get();
    }

    /**
     * Check if a person has active assignments and is active.
     *
     * @param int $person_id
     * @return bool
     */
    public function isActive($person_id)
    {
        $now = now();
        
        return Post::where('ID', $person_id)
            ->published()
            ->type('person')
            ->whereHas('personAssignments', function($query) use ($now) {
                $query->where('period_start', '<=', $now)
                      ->where('period_end', '>=', $now);
            })
            ->exists();
    }
}
