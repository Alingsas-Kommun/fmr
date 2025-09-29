<?php

namespace App\Http\Controllers;

use App\Models\DecisionAuthority;
use App\Http\Controllers\Admin\TypeController;
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
        $query = DecisionAuthority::with(['board', 'typeTerm'])
            ->active();

        // Filter by type if provided
        if ($request->filled('type')) {
            $query->whereHas('typeTerm', function($q) use ($request) {
                $q->where('name', $request->type);
            });
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

        // Get type terms for filter dropdown
        $typeController = app(TypeController::class);
        $typeTerms = $typeController->getAll();

        return view('decision-authorities.index', [
            'decisionAuthorities' => $decisionAuthorities,
            'typeTerms' => $typeTerms,
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
        // Load the typeTerm relationship
        $decisionAuthority->load('typeTerm');
        
        $activeAssignments = $decisionAuthority->assignments()
            ->with('person', 'roleTerm', 'board')
            ->active()
            ->get();

        return view('decision-authorities.show', [
            'decisionAuthority' => $decisionAuthority,
            'activeAssignments' => $activeAssignments
        ]);
    }
}
