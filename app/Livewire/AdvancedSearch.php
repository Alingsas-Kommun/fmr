<?php

namespace App\Livewire;

use App\Http\Controllers\SearchController;
use App\Models\Post;
use App\Models\Term;
use Livewire\Attributes\Url;
use Livewire\Component;

class AdvancedSearch extends Component
{
    /**
     * The search query.
     */
    #[Url]
    public string $query = '';

    /**
     * Filter properties.
     */
    #[Url]
    public ?int $boardId = null;
    
    #[Url]
    public ?int $partyId = null;
    
    #[Url]
    public ?int $roleId = null;

    /**
     * Search results.
     */
    public $results;

    /**
     * Filter options.
     */
    public $filters = [];

    /**
     * Mount the component.
     */
    public function mount()
    {
        $this->results = collect();
        $this->loadFilters();
        
        // Initialize properties from URL if they exist
        if (request()->has('q')) {
            $this->query = request()->get('q') ?? '';
        }
        if (request()->has('boardId')) {
            $this->boardId = (int) request()->get('boardId');
        }
        if (request()->has('partyId')) {
            $this->partyId = (int) request()->get('partyId');
        }
        if (request()->has('roleId')) {
            $this->roleId = (int) request()->get('roleId');
        }
        
        // Only perform search if there's a query or filters
        if ($this->hasSearchCriteria()) {
            $this->performSearch();
        }
    }


    /**
     * Updated query - perform search when query changes.
     */
    public function updatedQuery()
    {
        $this->performSearch();
    }

    /**
     * Updated filters - perform search when filters change.
     */
    public function updatedBoardId()
    {
        $this->performSearch();
    }

    public function updatedPartyId()
    {
        $this->performSearch();
    }

    public function updatedRoleId()
    {
        $this->performSearch();
    }

    /**
     * Perform the search using SearchController.
     */
    public function performSearch()
    {
        if (!$this->hasSearchCriteria()) {
            $this->results = collect();
            return;
        }

        $searchController = app(SearchController::class);
        $this->results = $searchController->advancedSearch($this->query, $this->boardId, $this->partyId, $this->roleId);
    }

    /**
     * Load filter options.
     */
    private function loadFilters()
    {
        $this->filters = [
            'boards' => Post::boards()
                ->published()
                ->orderBy('post_title')
                ->get(['ID', 'post_title']),
            'parties' => Post::parties()
                ->published()
                ->orderBy('post_title')
                ->get(['ID', 'post_title']),
            'roles' => Term::whereHas('termTaxonomy', function ($q) {
                $q->where('taxonomy', 'role');
            })->orderBy('name')->get(['term_id', 'name']),
        ];
    }

    /**
     * Clear all filters and search query.
     */
    public function clearFilters()
    {
        $this->query = '';
        $this->boardId = null;
        $this->partyId = null;
        $this->roleId = null;
        $this->results = collect();
        
        // Redirect to clean URL without parameters
        return $this->redirect(route('search.show'));
    }

    /**
     * Check if there are any search criteria (query or filters).
     */
    private function hasSearchCriteria(): bool
    {
        return !empty($this->query) || 
               !is_null($this->boardId) || 
               !is_null($this->partyId) || 
               !is_null($this->roleId);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.advanced-search');
    }
}