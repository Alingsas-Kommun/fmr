<?php

namespace App\Http\Controllers\Admin;

use App\Models\Term;

class TypeController
{
    /**
     * Get all types for filter dropdown.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Term::whereHas('termTaxonomy', function($q) {
            $q->where('taxonomy', 'type');
        })->orderBy('name')->get();
    }
}
