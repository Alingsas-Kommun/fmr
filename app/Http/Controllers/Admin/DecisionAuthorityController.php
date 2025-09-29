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
        return DecisionAuthority::with(['board', 'typeTerm'])
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
            ->with(['board', 'typeTerm'])
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
        $data = $request->only([
            'board_id',
            'title',
            'type_term_id',
            'start_date',
            'end_date'
        ]);

        // Set the current user as the author
        $data['author_id'] = \get_current_user_id();

        return DecisionAuthority::create($data);
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
            'type_term_id',
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
        return [
            'all' => DecisionAuthority::count(),
            'ongoing' => DecisionAuthority::active()->count(),
            'past' => DecisionAuthority::inactive()->count()
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
        $query = $this->buildFilteredQuery($args);

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

    /**
     * Build a filtered and sorted query for decision authorities.
     * This method is shared between pagination and export.
     *
     * @param array $args
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildFilteredQuery($args = [])
    {
        $query = DecisionAuthority::with(['board', 'typeTerm', 'author']);

        $this->applySorting($query, $args);
        $this->applyFilters($query, $args);

        return $query;
    }

    /**
     * Apply sorting to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $args
     * @return void
     */
    private function applySorting($query, $args)
    {
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
                $query->join('terms', 'decision_authority.type_term_id', '=', 'terms.term_id')
                    ->orderBy('terms.name', $order);
                break;
            case 'author':
                $query->join('users as author', 'decision_authority.author_id', '=', 'author.ID')
                    ->orderBy('author.display_name', $order);
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }
    }

    /**
     * Apply filters to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $args
     * @return void
     */
    private function applyFilters($query, $args)
    {
        // Handle period status filter
        $period_status = $args['period_status'] ?? 'all';

        if ($period_status === 'ongoing') {
            $query->ongoing();
        } elseif ($period_status === 'past') {
            $query->past();
        }

        // Handle additional filters
        if (!empty($args['board_filter'])) {
            $query->where('board_id', $args['board_filter']);
        }

        if (!empty($args['start_date'])) {
            $query->where('start_date', '<=', $args['start_date'])
                  ->where(function($q) use ($args) {
                      $q->where('end_date', '>=', $args['start_date'])
                        ->orWhereNull('end_date');
                  });
        }

        if (!empty($args['end_date'])) {
            $query->where('start_date', '<=', $args['end_date'])
                  ->where(function($q) use ($args) {
                      $q->where('end_date', '>=', $args['end_date'])
                        ->orWhereNull('end_date');
                  });
        }

        if (!empty($args['author_filter'])) {
            $query->where('author_id', $args['author_filter']);
        }

        // Handle search
        $search = $args['search'] ?? '';
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhereHas('typeTerm', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('board', function($q) use ($search) {
                        $q->where('post_title', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('author', function($q) use ($search) {
                        $q->where('display_name', 'like', '%' . $search . '%')
                          ->orWhere('user_login', 'like', '%' . $search . '%');
                    })
                    ->orWhere('start_date', 'like', '%' . $search . '%')
                    ->orWhere('end_date', 'like', '%' . $search . '%');
            });
        }
    }
}
