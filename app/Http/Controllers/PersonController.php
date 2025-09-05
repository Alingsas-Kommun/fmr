<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Number of items to show per page
     */
    protected $perPage = 12;

    /**
     * Display a listing of persons.
     */
    public function index(Request $request)
    {
        // Get current page from request
        $currentPage = max(1, (int) $request->get('page', 1));
        $offset = ($currentPage - 1) * $this->perPage;

        // Get total count for pagination
        $totalCount = Post::ofType('person')
            ->published()
            ->count();

        // Get persons for current page
        $persons = Post::ofType('person')
            ->published()
            ->orderBy('post_title')
            ->offset($offset)
            ->limit($this->perPage)
            ->get();

        // Calculate pagination data
        $lastPage = ceil($totalCount / $this->perPage);
        $hasMorePages = $currentPage < $lastPage;
        $hasPreviousPages = $currentPage > 1;

        // Prepare pagination array
        $pagination = [
            'total' => $totalCount,
            'per_page' => $this->perPage,
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'has_more_pages' => $hasMorePages,
            'has_previous_pages' => $hasPreviousPages,
            'from' => $offset + 1,
            'to' => min($offset + $this->perPage, $totalCount)
        ];

        return view('persons.index', compact('persons', 'pagination'));
    }

    /**
     * Display the specified person.
     */
    public function show(Post $person)
    {
        if ($person->post_type !== 'person') {
            abort(404);
        }

        $assignments = $person->activeAssignments;
        $thumbnail = $person->thumbnail();

        return view('persons.show', compact('person', 'assignments', 'thumbnail'));
    }
}