<?php

namespace App\Http\Controllers\Api;

use App\Models\Assignment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Assignment::with(['roleTerm', 'board']);

            // Apply filters
            if ($request->filled('decision_authority_id')) {
                $query->where('decision_authority_id', $request->decision_authority_id);
            }

            if ($request->filled('person_id')) {
                $query->where('person_id', $request->person_id);
            }

            if ($request->filled('role_term_id')) {
                $query->where('role_term_id', $request->role_term_id);
            }

            if ($request->boolean('active')) {
                $query->active();
            }

            // Date range filters
            if ($request->filled('period_start_from')) {
                $query->where('period_start', '>=', $request->period_start_from);
            }

            if ($request->filled('period_start_to')) {
                $query->where('period_start', '<=', $request->period_start_to);
            }

            if ($request->filled('period_end_from')) {
                $query->where('period_end', '>=', $request->period_end_from);
            }

            if ($request->filled('period_end_to')) {
                $query->where('period_end', '<=', $request->period_end_to);
            }

            // Default sorting
            $query->orderBy('period_start', 'desc');

            $perPage = min($request->input('per_page', 15), 50);
            $page = $request->input('page', 1);
            
            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();

            return response()->json([
                'data' => Assignment::toApiCollection($items),
                'meta' => [
                    'current_page' => intval($page),
                    'per_page' => intval($perPage),
                    'total' => intval($total),
                    'pages' => intval(ceil($total / $perPage))
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $assignment = Assignment::with(['roleTerm', 'board'])
                ->findOrFail($id);

            return response()->json([
                'data' => $assignment->toApiFormat()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
