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
            $query = Assignment::with(['board' => function($q) {
                $q->select('ID', 'post_title', 'post_type');
            }, 'person' => function($q) {
                $q->select('ID', 'post_title', 'post_type');
            }]);

            // Apply filters
            if ($request->filled('board_id')) {
                $query->where('board_id', $request->board_id);
            }

            if ($request->filled('person_id')) {
                $query->where('person_id', $request->person_id);
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->boolean('active')) {
                $today = now()->toDateString();
                $query->where('period_start', '<=', $today)
                      ->where('period_end', '>=', $today);
            }

            // Default sorting
            $query->latest();

            $perPage = min($request->input('per_page', 15), 50);
            $page = $request->input('page', 1);
            
            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();

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
            $assignment = Assignment::with(['board', 'person'])->findOrFail($id);

            return response()->json([
                'data' => $assignment
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
