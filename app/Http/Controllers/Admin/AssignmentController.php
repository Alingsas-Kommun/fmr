<?php

namespace App\Http\Controllers\Admin;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController
{
    /**
     * Display the specified assignment.
     *
     * @param int $id
     * @return Assignment
     */
    public function show($id)
    {
        return Assignment::findOrFail($id);
    }

    /**
     * Store a new assignment.
     *
     * @param Request $request
     * @return Assignment
     */
    public function store(Request $request)
    {
        return Assignment::create($request->only([
            'person_id',
            'board_id',
            'role',
            'period_start',
            'period_end'
        ]));
    }

    /**
     * Update an existing assignment.
     *
     * @param Request $request
     * @param int $id
     * @return Assignment
     */
    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);
        
        $assignment->update($request->only([
            'person_id',
            'board_id',
            'role',
            'period_start',
            'period_end'
        ]));

        return $assignment;
    }

    /**
     * Delete an assignment.
     *
     * @param int $id
     * @return bool
     */
    public function destroy($id)
    {
        return Assignment::findOrFail($id)->delete();
    }
}
