<?php

namespace App\Http\Controllers;

use App\Models\DecisionAuthority;

class DecisionAuthorityController extends Controller
{
    /**
     * Display the specified decision authority.
     *
     * @param DecisionAuthority $decisionAuthority
     * @return \Illuminate\View\View
     */
    public function show(DecisionAuthority $decisionAuthority)
    {        
        $decisionAuthority->load('board.categoryTerm');

        if (!is_user_logged_in() && !$decisionAuthority->isActive()) {
            abort(404);
        }
        
        $activeAssignments = $decisionAuthority->assignments()
            ->with('person', 'roleTerm', 'board', 'person.party')
            ->active()
            ->get()
            ->sortBy(function ($assignment) {
                return (int) ($assignment->roleTerm->term_order ?? 9999);
            })
            ->values();

        $assignments = $activeAssignments->map(function ($assignment) {
            return (object) [
                'id' => $assignment->id,
                'person' => [
                    'url' => get_permalink($assignment->person->ID),
                    'text' => $assignment->person->post_title,
                    'image' => $assignment->person->thumbnail('size-6'),
                    'party' => [
                        'title' => $assignment->person->party?->post_title,
                        'url' => get_permalink($assignment->person->party?->ID),
                        'thumbnail' => $assignment->person->party?->thumbnail('size-4'),
                    ],
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
