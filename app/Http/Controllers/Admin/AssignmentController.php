<?php

namespace App\Http\Controllers\Admin;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController
{
    /**
     * Display the specified assignment.
     *
     * @param int $id
     * @return Assignment
     */
    public function show($id)
    {
        return Assignment::findOrFail($id);
    }

    /**
     * Store a new assignment.
     *
     * @param Request $request
     * @return Assignment
     */
    public function store(Request $request)
    {
        return Assignment::create($request->only([
            'person_id',
            'decision_authority_id',
            'role',
            'period_start',
            'period_end'
        ]));
    }

    /**
     * Update an existing assignment.
     *
     * @param Request $request
     * @param int $id
     * @return Assignment
     */
    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);
        
        $assignment->update($request->only([
            'person_id',
            'decision_authority_id',
            'role',
            'period_start',
            'period_end'
        ]));

        return $assignment;
    }

    /**
     * Delete an assignment.
     *
     * @param int $id
     * @return bool
     */
    public function destroy($id)
    {
        return Assignment::findOrFail($id)->delete();
    }

    /**
     * Get an assignment for editing or create a new one.
     *
     * @param int|null $id
     * @return Assignment
     */
    public function edit($id = null)
    {
        return $id ? $this->show($id) : new Assignment();
    }

    /**
     * Get counts for different assignment statuses.
     *
     * @return array
     */
    public function getStatusCounts()
    {
        $today = date('Y-m-d');

        return [
            'all' => Assignment::count(),
            'ongoing' => Assignment::where(function($query) use ($today) {
                $query->where('period_start', '<=', $today)
                    ->where(function($q) use ($today) {
                        $q->where('period_end', '>=', $today)
                            ->orWhereNull('period_end');
                    });
            })->count(),
            'past' => Assignment::where('period_end', '<', $today)->count()
        ];
    }

    /**
     * Get assignments for a specific person.
     *
     * @param int $person_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPersonsAssignments($person_id)
    {
        return Assignment::where('person_id', $person_id)
            ->with('decisionAuthority')
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Get paginated assignments with filters and sorting.
     *
     * @param array $args
     * @return array
     */
    public function getPaginatedAssignments($args = [])
    {
        $query = Assignment::with(['person', 'board', 'decisionAuthority']);

        // Handle sorting
        $orderby = $args['orderby'] ?? 'id';
        $order = isset($args['order']) ? strtolower($args['order']) : 'desc';

        switch ($orderby) {
            case 'person':
                $query->join('posts as person', 'assignments.person_id', '=', 'person.ID')
                    ->orderBy('person.post_title', $order);
                break;
            case 'board':
                $query->join('posts as board', 'assignments.board_id', '=', 'board.ID')
                    ->orderBy('board.post_title', $order);
                break;
            case 'role':
                $query->orderBy('role', $order);
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        // Handle period status filter
        $period_status = $args['period_status'] ?? 'all';
        $today = date('Y-m-d');

        if ($period_status === 'ongoing') {
            $query->where(function($q) use ($today) {
                $q->where('period_start', '<=', $today)
                    ->where(function($q) use ($today) {
                        $q->where('period_end', '>=', $today)
                            ->orWhereNull('period_end');
                    });
            });
        } elseif ($period_status === 'past') {
            $query->where('period_end', '<', $today);
        }

        // Handle search
        $search = $args['search'] ?? '';
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('person', function($q) use ($search) {
                    $q->where('post_title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('board', function($q) use ($search) {
                    $q->where('post_title', 'like', '%' . $search . '%');
                })
                ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        // Get total before pagination
        $total_items = $query->count();

        // Handle pagination
        $per_page = $args['per_page'] ?? 15;
        $current_page = $args['current_page'] ?? 1;

        $items = $query->skip(($current_page - 1) * $per_page)
            ->take($per_page)
            ->get();

        return [
            'items' => $items,
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ];
    }
}
