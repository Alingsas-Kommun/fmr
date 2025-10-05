<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment)
    {
        $assignment->load(['board', 'decisionAuthority', 'person']);

        return view('assignments.show', [
            'assignment' => $assignment
        ]);
    }
}
