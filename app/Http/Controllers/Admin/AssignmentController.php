<?php

namespace App\Http\Controllers\Admin;

use App\Models\Assignment;
use App\Services\AssignmentExportService;
use Illuminate\Http\Request;

class AssignmentController
{
    /**
     * Get all assignments.
     */
    public function getAll()
    {
        return Assignment::with(['person', 'decisionAuthority', 'roleTerm'])->get();
    }

    /**
     * Get assignments for a specific decision authority.
     */
    public function getByDecisionAuthority($decision_authority_id)
    {
        return Assignment::where('decision_authority_id', $decision_authority_id)
            ->with(['person', 'roleTerm'])
            ->orderBy('period_start', 'desc')
            ->get();
    }

    /**
     * Delete all assignments for a decision authority.
     */
    public function deleteByDecisionAuthority($decision_authority_id)
    {
        return Assignment::where('decision_authority_id', $decision_authority_id)->delete();
    }

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
        $data = $request->only([
            'person_id',
            'decision_authority_id',
            'role_term_id',
            'period_start',
            'period_end'
        ]);

        // Set the current user as the author
        $data['author_id'] = \get_current_user_id();

        return Assignment::create($data);
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
            'role_term_id',
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
        return [
            'all' => Assignment::count(),
            'ongoing' => Assignment::active()->count(),
            'past' => Assignment::inactive()->count()
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
     * Build a filtered and sorted query for assignments.
     * This method is shared between pagination and export.
     *
     * @param array $args
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildFilteredQuery($args = [])
    {
        $query = Assignment::with(['person', 'board', 'decisionAuthority', 'roleTerm', 'author']);

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
            case 'person':
                $query->join('posts as person', 'assignments.person_id', '=', 'person.ID')
                    ->orderBy('person.post_title', $order);
                break;
            case 'board':
                $query->join('decision_authority', 'assignments.decision_authority_id', '=', 'decision_authority.id')
                    ->join('posts as board', 'decision_authority.board_id', '=', 'board.ID')
                    ->orderBy('board.post_title', $order);
                break;
            case 'role':
                $query->join('terms as role_term', 'assignments.role_term_id', '=', 'role_term.term_id')
                    ->orderBy('role_term.name', $order);
                break;
            case 'decision_authority':
                $query->join('decision_authority', 'assignments.decision_authority_id', '=', 'decision_authority.id')
                    ->orderBy('decision_authority.title', $order);
                break;
            case 'period':
                // Sort by period_start first, then by period_end for same start dates
                $query->orderBy('period_start', $order)
                      ->orderBy('period_end', $order);
                break;
            case 'author':
                $query->join('users as author', 'assignments.author_id', '=', 'author.ID')
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
            $query->active();
        } elseif ($period_status === 'past') {
            $query->inactive();
        }

        // Handle additional filters
        if (!empty($args['role_filter'])) {
            $query->where('role_term_id', $args['role_filter']);
        }

        if (!empty($args['board_filter'])) {
            $query->whereHas('decisionAuthority', function($q) use ($args) {
                $q->where('board_id', $args['board_filter']);
            });
        }

        if (!empty($args['person_filter'])) {
            $query->where('person_id', $args['person_filter']);
        }

        if (!empty($args['party_filter'])) {
            $query->whereHas('person', function($q) use ($args) {
                $q->whereHas('meta', function($q) use ($args) {
                    $q->where('meta_key', 'person_party')
                      ->where('meta_value', $args['party_filter']);
                });
            });
        }

        $hasStart = !empty($args['period_start']);
        $hasEnd = !empty($args['period_end']);

        if ($hasStart && $hasEnd) {
            $filterStart = $args['period_start'];
            $filterEnd = $args['period_end'];

            $query->where('period_start', '<=', $filterEnd)
                  ->where(function($q) use ($filterStart) {
                      $q->where('period_end', '>=', $filterStart)
                        ->orWhereNull('period_end');
                  });
        } elseif ($hasStart) {
            $query->where('period_start', '<=', $args['period_start'])
                  ->where(function($q) use ($args) {
                      $q->where('period_end', '>=', $args['period_start'])
                        ->orWhereNull('period_end');
                  });
        } elseif ($hasEnd) {
            $query->where('period_start', '<=', $args['period_end'])
                  ->where(function($q) use ($args) {
                      $q->where('period_end', '>=', $args['period_end'])
                        ->orWhereNull('period_end');
                  });
        }

        if (!empty($args['author_filter'])) {
            $query->where('author_id', $args['author_filter']);
        }

        // Handle search
        $search = $args['search'] ?? '';
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('person', function($q) use ($search) {
                    $q->where('post_title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('decisionAuthority.board', function($q) use ($search) {
                    $q->where('post_title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('decisionAuthority', function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('roleTerm', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('author', function($q) use ($search) {
                    $q->where('display_name', 'like', '%' . $search . '%')
                      ->orWhere('user_login', 'like', '%' . $search . '%');
                })
                ->orWhere('period_start', 'like', '%' . $search . '%')
                ->orWhere('period_end', 'like', '%' . $search . '%');
            });
        }
    }


    /**
     * Handle export requests for Excel and CSV formats.
     * Delegates to the dedicated export service.
     *
     * @param string $format
     * @param array $filters
     * @return void
     */
    public function handleExport(string $format)
    {
        $filters = $_REQUEST;
        unset($filters['export']);
        unset($filters['_wpnonce']);

        $exportService = app(AssignmentExportService::class, [$this]);
        $exportService->handleExport($format, $filters);
    }
}
