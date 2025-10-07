<?php

namespace App\Http\Controllers;

use App\Models\Assignment;

class AssignmentController extends Controller
{
    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment)
    {
        $assignment->load(['board', 'decisionAuthority', 'person']);

        if (!is_user_logged_in() && !$assignment->isActive()) { // @phpstan-ignore-line
            abort(404);
        }

        return view('assignments.show', [
            'assignment' => $assignment
        ]);
    }
}
