<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Post::parties()
                ->published()
                ->with(['meta']);

            // Default sorting
            $query->orderBy('post_title');

            $perPage = min($request->input('per_page', 15), 50);
            $page = $request->input('page', 1);
            
            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();

            return response()->json([
                'data' => Post::formatCollection($items),
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
            $party = Post::parties()
                ->published()
                ->with(['meta'])
                ->findOrFail($id);

            return response()->json([
                'data' => $party->format(true)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
