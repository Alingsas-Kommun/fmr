<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Display search results for a specific term.
     */
    public function search(Request $request): View
    {
        return view('search.show');
    }

    /**
     * Perform simple search for autocomplete suggestions.
     */
    public function simpleSearch(string $query)
    {
        if (empty($query)) {
            return collect();
        }

        $persons = $this->buildSearchQuery($query)
            ->with([
                'meta' => function ($q) {
                    $q->whereIn('meta_key', ['person_firstname', 'person_lastname']);
                },
            ])
            ->limit(10)
            ->get();

        return $this->transformResults($persons);
    }

    /**
     * Perform advanced search with filters.
     */
    public function advancedSearch(string $query, ?int $boardId = null, ?int $partyId = null, ?int $roleId = null, ?string $sortBy = null, string $sortDirection = 'asc')
    {
        // If no search criteria provided, return empty results
        if (empty($query) && is_null($boardId) && is_null($partyId) && is_null($roleId)) {
            return collect();
        }

        $personsQuery = Post::persons()
            ->with('party')
            ->published()
            ->whereHas('personAssignments', function ($assignQ) {
                $assignQ->active();
            });

        // Add search criteria if query is provided
        if (!empty($query)) {
            $this->applySearchCriteria($personsQuery, $query);
        }

        // Apply filters
        $this->applyFilters($personsQuery, $boardId, $partyId, $roleId);

        // Execute query with eager loading
        $persons = $personsQuery->with([
                'meta' => function ($q) {
                    $q->whereIn('meta_key', ['person_firstname', 'person_lastname']);
                },
                'personAssignments.decisionAuthority.board',
                'personAssignments.roleTerm'
            ])
            ->get();

        $results = $this->transformResults($persons);
        
        if ($sortBy) {
            $results = $this->sortResults($results, $sortBy, $sortDirection);
        }

        return $results;
    }

    /**
     * Build the base search query with all search criteria.
     */
    private function buildSearchQuery(string $query)
    {
        $queryBuilder = Post::persons()
            ->with('party')
            ->published()
            ->whereHas('personAssignments', function ($assignQ) {
                $assignQ->active();
            });

        // Apply search criteria
        if (!empty($query)) {
            $this->applySearchCriteria($queryBuilder, $query);
        }

        return $queryBuilder;
    }

    /**
     * Apply comprehensive search criteria to the query.
     */
    private function applySearchCriteria($queryBuilder, string $query): void
    {
        $queryBuilder->where(function ($q) use ($query) {
            // Search by post title
            $q->where('post_title', 'like', '%' . $query . '%')
              // Search by firstname
              ->orWhereHas('meta', function ($metaQ) use ($query) {
                  $metaQ->where('meta_key', 'person_firstname')
                        ->where('meta_value', 'like', '%' . $query . '%');
              })
              // Search by lastname
              ->orWhereHas('meta', function ($metaQ) use ($query) {
                  $metaQ->where('meta_key', 'person_lastname')
                        ->where('meta_value', 'like', '%' . $query . '%');
              })
              // Search by party name
              ->orWhereHas('meta', function ($metaQ) use ($query) {
                  $metaQ->where('meta_key', 'person_party')
                        ->whereIn('meta_value', function ($subQ) use ($query) {
                            $subQ->select('ID')
                                 ->from('posts')
                                 ->where('post_type', 'party')
                                 ->where('post_title', 'like', '%' . $query . '%');
                        });
              })
              // Search by role (active assignments only)
              ->orWhereHas('personAssignments', function ($assignQ) use ($query) {
                  $assignQ->active()
                          ->whereHas('roleTerm', function ($roleQ) use ($query) {
                              $roleQ->where('name', 'like', '%' . $query . '%');
                          });
              })
              // Search by board (active assignments only)
              ->orWhereHas('personAssignments', function ($assignQ) use ($query) {
                  $assignQ->active()
                          ->whereHas('decisionAuthority', function ($daQ) use ($query) {
                              $daQ->whereHas('board', function ($boardQ) use ($query) {
                                  $boardQ->where('post_title', 'like', '%' . $query . '%');
                              });
                          });
              });
        });
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, ?int $boardId, ?int $partyId, ?int $roleId): void
    {
        // Apply board filter (only active assignments)
        if ($boardId) {
            $query->whereHas('personAssignments', function ($q) use ($boardId) {
                $q->active()
                  ->whereHas('decisionAuthority', function ($subQ) use ($boardId) {
                      $subQ->where('board_id', $boardId);
                  });
            });
        }

        // Apply party filter
        if ($partyId) {
            $query->whereHas('meta', function ($q) use ($partyId) {
                $q->where('meta_key', 'person_party')
                  ->where('meta_value', $partyId);
            });
        }

        // Apply role filter (only active assignments)
        if ($roleId) {
            $query->whereHas('personAssignments', function ($q) use ($roleId) {
                $q->active()
                  ->where('role_term_id', $roleId);
            });
        }
    }


    /**
     * Transform search results.
     */
    private function transformResults($persons)
    {
        return $persons->map(function ($person) {
            $firstname = $person->getMeta('person_firstname');
            $lastname = $person->getMeta('person_lastname');
            $fullName = trim($firstname . ' ' . $lastname);
            
            // Get party from BelongsToMeta relationship
            $party = $person->party;
            
            return (object) [
                'id' => $person->ID,
                'title' => $fullName,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'thumbnail' => $person->thumbnail(),
                'url' => get_permalink($person->ID),
                'type' => 'person',
                'party' => $party ? (object) [
                    'id' => $party->ID,
                    'title' => $party->post_title,
                    'thumbnail' => $party->thumbnail('w-4 h-4'),
                    'url' => get_permalink($party->ID),
                ] : null,
            ];
        });
    }

    /**
     * Sort the results by the specified column and direction.
     */
    private function sortResults($results, string $sortBy, string $sortDirection)
    {
        return $results->sortBy(function ($item) use ($sortBy) {
            // Handle nested properties like party.title
            if (str_contains($sortBy, '.')) {
                $keys = explode('.', $sortBy);
                $value = $item;
                foreach ($keys as $key) {
                    $value = $value->{$key} ?? '';
                }
                return $value;
            }
            
            return $item->{$sortBy} ?? '';
        }, SORT_REGULAR, $sortDirection === 'desc');
    }
}
