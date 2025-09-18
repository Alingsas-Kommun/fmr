<?php

namespace App\Http\Controllers\Admin;

use App\Models\DecisionAuthority;
use Illuminate\Http\Request;

class DecisionAuthorityController
{
    /**
     * Get all decision authorities with their associated boards.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return DecisionAuthority::with('board')
            ->orderBy('title')
            ->get();
    }

    /**
     * Get decision authorities for a specific board.
     *
     * @param int $board_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDecisionAuthoritiesForBoard($board_id)
    {
        return DecisionAuthority::where('board_id', $board_id)
            ->with('board')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Display the specified decision authority.
     *
     * @param int $id
     * @return DecisionAuthority
     */
    public function show($id)
    {
        return $id ? DecisionAuthority::findOrFail($id) : new DecisionAuthority();
    }

    /**
     * Store a new decision authority.
     *
     * @param Request $request
     * @return DecisionAuthority
     */
    public function store(Request $request)
    {
        return DecisionAuthority::create($request->only([
            'board_id',
            'title',
            'type',
            'start_date',
            'end_date'
        ]));
    }

    /**
     * Update an existing decision authority.
     *
     * @param Request $request
     * @param int $id
     * @return DecisionAuthority
     */
    public function update(Request $request, $id)
    {
        $decisionAuthority = DecisionAuthority::findOrFail($id);
        
        $decisionAuthority->update($request->only([
            'board_id',
            'title',
            'type',
            'start_date',
            'end_date'
        ]));

        return $decisionAuthority;
    }

    /**
     * Delete a decision authority.
     *
     * @param int $id
     * @return bool
     */
    public function destroy($id)
    {
        return DecisionAuthority::findOrFail($id)->delete();
    }

    /**
     * Get counts for different decision authority statuses.
     *
     * @return array
     */
    public function getStatusCounts()
    {
        $today = date('Y-m-d');

        return [
            'all' => DecisionAuthority::count(),
            'ongoing' => DecisionAuthority::where(function($query) use ($today) {
                $query->where('start_date', '<=', $today)
                    ->where(function($q) use ($today) {
                        $q->where('end_date', '>=', $today)
                            ->orWhereNull('end_date');
                    });
            })->count(),
            'past' => DecisionAuthority::where('end_date', '<', $today)->count()
        ];
    }

    /**
     * Get paginated decision authorities with filters and sorting.
     *
     * @param array $args
     * @return array
     */
    public function getPaginatedDecisionAuthorities($args = [])
    {
        $query = DecisionAuthority::with('board');

        // Handle sorting
        $orderby = $args['orderby'] ?? 'id';
        $order = isset($args['order']) ? strtolower($args['order']) : 'desc';

        switch ($orderby) {
            case 'title':
                $query->orderBy('title', $order);
                break;
            case 'board':
                $query->join('posts', 'decision_authority.board_id', '=', 'posts.ID')
                    ->orderBy('posts.post_title', $order);
                break;
            case 'type':
                $query->orderBy('type', $order);
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $period_status = $args['period_status'] ?? 'all';
        $today = date('Y-m-d');

        if ($period_status === 'ongoing') {
            $query->where(function($q) use ($today) {
                $q->where('start_date', '<=', $today)
                    ->where(function($q) use ($today) {
                        $q->where('end_date', '>=', $today)
                            ->orWhereNull('end_date');
                    });
            });
        } elseif ($period_status === 'past') {
            $query->where('end_date', '<', $today);
        }

        if (!empty($args['board_filter'])) {
            $query->where('board_id', $args['board_filter']);
        }

        if (!empty($args['start_date'])) {
            $query->where('start_date', '>=', $args['start_date']);
        }

        if (!empty($args['end_date'])) {
            $query->where('end_date', '<=', $args['end_date']);
        }

        // Handle search
        $search = $args['search'] ?? '';
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%')
                    ->orWhereHas('board', function($q) use ($search) {
                        $q->where('post_title', 'like', '%' . $search . '%');
                    })
                    ->orWhere('start_date', 'like', '%' . $search . '%')
                    ->orWhere('end_date', 'like', '%' . $search . '%');
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
