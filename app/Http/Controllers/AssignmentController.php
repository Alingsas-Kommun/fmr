<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Term;
use App\Models\TermTaxonomy;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     */
    public function index(Request $request)
    {
        $query = Assignment::with(['board', 'decisionAuthority', 'person', 'roleTerm']);

        $today = now()->toDateString();
        $query->where('period_start', '<=', $today)
                ->where('period_end', '>=', $today);

        // Filter by role if provided
        if ($request->filled('role')) {
            $query->whereHas('roleTerm', function($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        // Default sorting by latest
        $query->latest();

        // Manual pagination
        $perPage = 15;
        $page = $request->input('page', 1);
        $total = $query->count();
        
        $assignments = $query->skip(($page - 1) * $perPage)
                           ->take($perPage)
                           ->get();

        $roleTerms = Term::whereHas('termTaxonomy', function($q) {
            $q->where('taxonomy', 'role');
        })->orderBy('name')->get();

        return view('assignments.index', [
            'assignments' => $assignments,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage)
            ],
            'filters' => [
                'role' => $request->role,
            ],
            'roleTerms' => $roleTerms
        ]);
    }

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
