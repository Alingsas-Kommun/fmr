<?php

namespace App\Http\Controllers;

use App\Models\DecisionAuthority;
use Illuminate\Http\Request;

class DecisionAuthorityController extends Controller
{
    /**
     * Display a listing of decision authorities.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = DecisionAuthority::with(['board']);

        // Only show active decision authorities
        $today = now()->toDateString();
        $query->where('start_date', '<=', $today)
              ->where('end_date', '>=', $today);

        // Filter by type if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Default sorting by latest
        $query->latest('start_date');

        // Pagination
        $perPage = 15;
        $page = $request->input('page', 1);
        $total = $query->count();
        
        $decisionAuthorities = $query->skip(($page - 1) * $perPage)
                                   ->take($perPage)
                                   ->get();

        return view('decision-authorities.index', [
            'decisionAuthorities' => $decisionAuthorities,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage)
            ],
            'filters' => [
                'type' => $request->type
            ]
        ]);
    }

    /**
     * Display the specified decision authority.
     *
     * @param DecisionAuthority $decisionAuthority
     * @return \Illuminate\View\View
     */
    public function show(DecisionAuthority $decisionAuthority)
    {        
        $today = now()->toDateString();
        $activeAssignments = $decisionAuthority->assignments()
            ->with('person', 'roleTerm', 'board')
            ->where('period_start', '<=', $today)
            ->where('period_end', '>=', $today)
            ->get();

        return view('decision-authorities.show', [
            'decisionAuthority' => $decisionAuthority,
            'activeAssignments' => $activeAssignments
        ]);
    }
}
