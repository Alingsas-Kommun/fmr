<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\DecisionAuthority;
use App\Models\Post;
use App\Models\Term;
use Livewire\Attributes\Url;
use Livewire\Component;

class Search extends Component
{
    /**
     * The search query.
     */
    #[Url]
    public string $query = '';

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $results = collect();
        
        if ($this->query) {
            $personsQuery = Post::persons()
                ->published()
                ->where('post_title', 'like', '%' . $this->query . '%')
                ->limit(10);

            $persons = $personsQuery->get();
            
            foreach ($persons as $person) {
                $results->push([
                    'id' => $person->ID,
                    'title' => $person->post_title,
                    'thumbnail' => $person->thumbnail(),
                    'url' => get_permalink($person->ID),
                ]);
            }
        }

        return view('livewire.search', compact('results'));
    }
}