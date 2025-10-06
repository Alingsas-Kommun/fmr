<?php

namespace App\Http\Controllers;

use App\Models\DecisionAuthority;

class DecisionAuthorityController extends Controller
{
    /**
     * Display the specified decision authority.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {        
        $decisionAuthority = DecisionAuthority::with('typeTerm')
            ->findOrFail($id);

        if (!is_user_logged_in() && !$decisionAuthority->isActive()) {
            abort(404);
        }
        
        $activeAssignments = $decisionAuthority->assignments()
            ->with('person', 'roleTerm', 'board')
            ->active()
            ->get();

        $assignments = $activeAssignments->map(function ($assignment) {
            return (object) [
                'id' => $assignment->id,
                'person' => [
                    'url' => get_permalink($assignment->person->ID),
                    'text' => $assignment->person->post_title,
                ],
                'role' => $assignment->roleTerm->name,
                'period' => date('Y-m-d', strtotime($assignment->period_start)) . ' – ' . date('Y-m-d', strtotime($assignment->period_end)),
                'view' => [
                    'url' => route('assignments.show', $assignment),
                    'text' => __('View', 'fmr'),
                ]
            ];
        })->toArray();

        return view('decision-authorities.show', [
            'decisionAuthority' => $decisionAuthority,
            'assignments' => $assignments
        ]);
    }
}
