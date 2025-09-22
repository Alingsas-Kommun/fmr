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
            ->with(['meta' => function ($q) {
                $q->whereIn('meta_key', ['person_firstname', 'person_lastname', 'person_party']);
            }])
            ->limit(10)
            ->get();

        return $this->transformSimpleResults($persons, $query);
    }

    /**
     * Perform advanced search with filters.
     */
    public function advancedSearch(string $query, ?int $boardId = null, ?int $partyId = null, ?int $roleId = null)
    {
        // If no search criteria provided, return empty results
        if (empty($query) && is_null($boardId) && is_null($partyId) && is_null($roleId)) {
            return collect();
        }

        $personsQuery = Post::persons()->published();

        // Add search criteria if query is provided
        if (!empty($query)) {
            $personsQuery->where(function ($q) use ($query) {
                $q->where('post_title', 'like', '%' . $query . '%')
                  ->orWhereHas('meta', function ($metaQ) use ($query) {
                      $metaQ->where('meta_key', 'person_firstname')
                            ->where('meta_value', 'like', '%' . $query . '%');
                  })
                  ->orWhereHas('meta', function ($metaQ) use ($query) {
                      $metaQ->where('meta_key', 'person_lastname')
                            ->where('meta_value', 'like', '%' . $query . '%');
                  });
            });
        }

        // Apply filters
        $this->applyFilters($personsQuery, $boardId, $partyId, $roleId);

        // Get party IDs for eager loading
        $partyIds = $this->getPartyIds($personsQuery);

        // Execute query with eager loading
        $persons = $personsQuery->with([
                'meta' => function ($q) {
                    $q->whereIn('meta_key', ['person_firstname', 'person_lastname', 'person_party']);
                },
                'personAssignments.decisionAuthority.board',
                'personAssignments.roleTerm'
            ])
            ->limit(50)
            ->get();

        // Eager load parties
        $parties = Post::whereIn('ID', $partyIds)->get()->keyBy('ID');

        return $this->transformAdvancedResults($persons, $parties);
    }

    /**
     * Build the base search query with all search criteria.
     */
    private function buildSearchQuery(string $query)
    {
        return Post::persons()
            ->published()
            ->where(function ($q) use ($query) {
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
                      $assignQ->where('period_start', '<=', now())
                              ->where('period_end', '>=', now())
                              ->whereHas('roleTerm', function ($roleQ) use ($query) {
                                  $roleQ->where('name', 'like', '%' . $query . '%');
                              });
                  })
                  // Search by board (active assignments only)
                  ->orWhereHas('personAssignments', function ($assignQ) use ($query) {
                      $assignQ->where('period_start', '<=', now())
                              ->where('period_end', '>=', now())
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
                $q->where('period_start', '<=', now())
                  ->where('period_end', '>=', now())
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
                $q->where('period_start', '<=', now())
                  ->where('period_end', '>=', now())
                  ->where('role_term_id', $roleId);
            });
        }
    }

    /**
     * Get party IDs for eager loading.
     */
    private function getPartyIds($query): array
    {
        return $query->get()
            ->pluck('meta')
            ->flatten()
            ->where('meta_key', 'person_party')
            ->pluck('meta_value')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Transform simple search results.
     */
    private function transformSimpleResults($persons, string $query)
    {
        // Get all party IDs from results
        $partyIds = $persons->pluck('meta')
            ->flatten()
            ->where('meta_key', 'person_party')
            ->pluck('meta_value')
            ->filter()
            ->unique()
            ->values();

        // Eager load all parties
        $parties = Post::whereIn('ID', $partyIds)->get()->keyBy('ID');

        return $persons->map(function ($person) use ($query, $parties) {
            $firstname = $person->getMeta('person_firstname');
            $lastname = $person->getMeta('person_lastname');
            $fullName = trim($firstname . ' ' . $lastname);
            
            // Get party from eager loaded collection
            $partyId = $person->getMeta('person_party');
            $party = $partyId ? $parties->get($partyId) : null;
            
            return (object) [
                'id' => $person->ID,
                'title' => $fullName,
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
     * Transform advanced search results.
     */
    private function transformAdvancedResults($persons, $parties)
    {
        return $persons->map(function ($person) use ($parties) {
            $firstname = $person->getMeta('person_firstname');
            $lastname = $person->getMeta('person_lastname');
            
            // Get party from eager loaded collection
            $partyId = $person->getMeta('person_party');
            $party = $partyId ? $parties->get($partyId) : null;
            
            return (object) [
                'id' => $person->ID,
                'title' => $person->post_title,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'party' => $party ? (object) [
                    'id' => $party->ID,
                    'title' => $party->post_title,
                    'thumbnail' => $party->thumbnail('w-4 h-4'),
                    'url' => get_permalink($party->ID),
                ] : null,
                'url' => get_permalink($person->ID),
            ];
        });
    }

}
