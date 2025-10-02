<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Post::boards()
                ->published()
                ->with(['meta', 'categoryTerm']);

            // Filter by category
            if ($request->filled('category_term_id')) {
                $query->withMeta('board_category', $request->category_term_id);
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
            $board = Post::boards()
                ->published()
                ->with(['meta', 'categoryTerm'])
                ->findOrFail($id);

            return response()->json([
                'data' => $board->format()->toArray(includeMeta: true)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
