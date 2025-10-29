<?php

namespace App\Livewire;

use App\Http\Controllers\SearchController;
use Livewire\Attributes\Url;
use Livewire\Component;

use function App\Core\setting;

class Search extends Component
{
    /**
     * The search query.
     */
    #[Url]
    public string $query = '';

    /**
     * Search results.
     */
    public $results;

    /**
     * Handle form submission - redirect to advanced search.
     */
    public function search()
    {
        if (empty($this->query)) {
            return;
        }

        if (! setting('show_advanced_search', true)) {
            return;
        }

        return redirect()->route('search.show', ['q' => $this->query]);
    }

    /**
     * Updated query - perform search when query changes.
     * 
     * @return void
     */
    public function updatedQuery()
    {
        $this->performSearch();
    }

    /**
     * Perform the search using SearchController.
     * 
     * @return void
     */
    public function performSearch()
    {
        if (empty($this->query)) {
            $this->results = collect();
            
            return;
        }

        $searchController = new SearchController();
        $this->results = $searchController->simpleSearch($this->query);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.search');
    }
}