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
            $query = Assignment::with([
                'decisionAuthority' => function($q) {
                    $q->select('id', 'title', 'board_id', 'type', 'start_date', 'end_date');
                }, 
                'person' => function($q) {
                    $q->select('ID', 'post_title');
                },
                'roleTerm' => function($q) {
                    $q->select('term_id', 'name');
                }
            ]);

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

            // Add computed role attribute to each item
            $items->each(function ($assignment) {
                $assignment->role = $assignment->role;
            });

            return response()->json([
                'data' => $items,
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $assignment = Assignment::with([
                'decisionAuthority' => function($q) {
                    $q->select('id', 'title', 'board_id', 'type', 'start_date', 'end_date');
                },
                'person' => function($q) {
                    $q->select('ID', 'post_title');
                },
                'roleTerm' => function($q) {
                    $q->select('term_id', 'name');
                }
            ])->findOrFail($id);

            $assignment->role = $assignment->role;

            return response()->json([
                'data' => $assignment
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
