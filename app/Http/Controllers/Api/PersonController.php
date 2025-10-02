<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Post::persons()
                ->published()
                ->with(['meta', 'party']);

            // Apply filters
            if ($request->filled('party_id')) {
                $query->withMeta('person_party', $request->party_id);
            }

            if ($request->filled('group_leader')) {
                $query->withMeta('person_group_leader', $request->group_leader ? '1' : '0');
            }

            if ($request->filled('has_active_assignments')) {
                if ($request->boolean('has_active_assignments')) {
                    $query->activeAssignments();
                } else {
                    $query->inactiveAssignments();
                }
            }

            // Search by name
            if ($request->filled('search')) {
                $query->where('post_title', 'like', '%' . $request->search . '%');
            }

            // Default sorting
            $query->orderBy('post_title');

            $perPage = min($request->input('per_page', 15), 50);
            $page = $request->input('page', 1);
            
            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();

            return response()->json([
                'data' => $items->apiFormat(),
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

    public function show(Request $request, $id)
    {
        try {
            $person = Post::persons()
                ->published()
                ->with(['meta', 'party'])
                ->findOrFail($id);

            return response()->json([
                'data' => $person->format()->toArray(includeMeta: true)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
