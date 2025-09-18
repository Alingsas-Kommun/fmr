<?php

namespace App\Http\Controllers\Admin;

use App\Models\Term;

class RoleController
{
    /**
     * Get all roles for filter dropdown.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Term::whereHas('termTaxonomy', function($q) {
            $q->where('taxonomy', 'role');
        })->orderBy('name')->get();
    }
}
