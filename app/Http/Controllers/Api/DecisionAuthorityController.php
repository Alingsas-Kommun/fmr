<?php

namespace App\Http\Controllers\Api;

use App\Models\DecisionAuthority;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DecisionAuthorityController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DecisionAuthority::with(['typeTerm']);

            // Apply filters
            if ($request->filled('board_id')) {
                $query->where('board_id', $request->board_id);
            }

            if ($request->filled('type_term_id')) {
                $query->where('type_term_id', $request->type_term_id);
            }

            if ($request->filled('active')) {
                if ($request->boolean('active')) {
                    $query->active();
                } else {
                    $query->inactive();
                }
            }

            // Date range filters
            if ($request->filled('start_date_from')) {
                $query->where('start_date', '>=', $request->start_date_from);
            }

            if ($request->filled('start_date_to')) {
                $query->where('start_date', '<=', $request->start_date_to);
            }

            if ($request->filled('end_date_from')) {
                $query->where('end_date', '>=', $request->end_date_from);
            }

            if ($request->filled('end_date_to')) {
                $query->where('end_date', '<=', $request->end_date_to);
            }

            // Search by title
            if ($request->filled('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            // Default sorting
            $query->orderBy('start_date', 'desc');

            $perPage = min($request->input('per_page', 15), 50);
            $page = $request->input('page', 1);
            
            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();

            return response()->json([
                'data' => DecisionAuthority::toApiCollection($items),
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
            $decisionAuthority = DecisionAuthority::with(['typeTerm'])
                ->findOrFail($id);

            return response()->json([
                'data' => $decisionAuthority->toApiFormat()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
