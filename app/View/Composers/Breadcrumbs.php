<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;
use Illuminate\Support\Facades\Route;

class Breadcrumbs extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        '*',
    ];

    public function with(): array
    {
        return [
            'breadcrumbs' => $this->buildBreadcrumbs()
        ];
    }

    /**
     * Build breadcrumbs by merging from different sources.
     * 
     * @return array
     */
    public function buildBreadcrumbs(): array
    {
        if ($this->view->getName() === '404') {
            return [];
        }

        $currentRoute = Route::currentRouteName();
        $routeBreadcrumbs = $this->getRouteBreadcrumbs($currentRoute);
        
        // If no route breadcrumbs and we're on homepage route, skip
        if (empty($routeBreadcrumbs) && ($currentRoute === 'homepage' || $currentRoute === null)) {
            return [];
        }

        $breadcrumbs = [
            [
                'label' => __('Home', 'fmr'),
                'url' => home_url('/'),
                'icon' => 'heroicon-o-home',
                'current' => false,
            ]
        ];
        
        $breadcrumbs = array_merge($breadcrumbs, $routeBreadcrumbs);

        return $breadcrumbs;
    }

    /**
     * Get breadcrumbs for specific routes.
     * 
     * @param string|null $routeName
     * @return array
     */
    private function getRouteBreadcrumbs(?string $routeName): array
    {
        if (!$routeName) {
            return [];
        }

        // Check if we're on a WordPress single post page
        if (is_singular()) { // @phpstan-ignore-line
            return $this->getWordPressBreadcrumbs();
        }

        // Laravel routes
        switch ($routeName) {
            case 'assignments.show':
                return $this->getAssignmentsShowBreadcrumbs();
            case 'decision-authorities.show':
                return $this->getDecisionAuthoritiesShowBreadcrumbs();
            case 'search.show':
                return $this->getSearchBreadcrumbs();
            case 'styleguide':
                return $this->getStyleguideBreadcrumbs();
            case 'homepage':
            default:
                return [];
        }
    }

    /**
     * Get breadcrumbs for WordPress single posts.
     * 
     * @return array
     */
    private function getWordPressBreadcrumbs(): array
    {
        $post = get_queried_object(); // @phpstan-ignore-line
        $post_type = get_post_type($post); // @phpstan-ignore-line

        $breadcrumbs = [];

        // Add post type breadcrumb
        switch ($post_type) {
            case 'person':
                $breadcrumbs[] = [
                    'label' => __('Persons', 'fmr'),
                    'url' => '#',
                    'icon' => 'heroicon-o-users',
                    'current' => false,
                ];
                break;
            case 'party':
                $breadcrumbs[] = [
                    'label' => __('Parties', 'fmr'),
                    'url' => '#',
                    'icon' => 'heroicon-o-user-group',
                    'current' => false,
                ];
                break;
            case 'board':
                $breadcrumbs[] = [
                    'label' => __('Boards', 'fmr'),
                    'url' => '#',
                    'icon' => 'heroicon-o-building-office-2',
                    'current' => false,
                ];
                break;
        }

        // Add current post
        $breadcrumbs[] = [
            'label' => get_the_title($post), // @phpstan-ignore-line
            'url' => get_permalink($post), // @phpstan-ignore-line
            'icon' => null,
            'current' => true,
        ];

        return $breadcrumbs;
    }

    /**
     * Get breadcrumbs for assignments show.
     * 
     * @return array
     */
    private function getAssignmentsShowBreadcrumbs(): array
    {
        $breadcrumbs = [
            [
                'label' => __('Assignments', 'fmr'),
                'url' => '#',
                'icon' => 'heroicon-o-clipboard-document-list',
                'current' => false,
            ]
        ];

        // Get the assignment data from the route parameter
        $assignment = request()->route('assignment');
        if ($assignment && is_object($assignment)) {
            // Load the roleTerm relationship if not already loaded
            if (!$assignment->relationLoaded('roleTerm')) {
                $assignment->load('roleTerm');
            }
            
            if ($assignment->roleTerm) {
                $breadcrumbs[] = [
                    'label' => sprintf(__('%s (%s)', 'fmr'), $assignment->roleTerm->name, $assignment->person->post_title),
                    'url' => '#',
                    'icon' => null,
                    'current' => true,
                ];
            } else {
                $breadcrumbs[] = [
                    'label' => __('Assignment Details', 'fmr'),
                    'url' => '#',
                    'icon' => null,
                    'current' => true,
                ];
            }
        } else {
            $breadcrumbs[] = [
                'label' => __('Assignment Details', 'fmr'),
                'url' => '#',
                'icon' => null,
                'current' => true,
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Get breadcrumbs for decision authorities show.
     * 
     * @return array
     */
    private function getDecisionAuthoritiesShowBreadcrumbs(): array
    {
        $breadcrumbs = [
            [
                'label' => __('Decision Authorities', 'fmr'),
                'url' => '#',
                'icon' => 'heroicon-o-scale',
                'current' => false,
            ]
        ];

        // Get the decision authority data from the route parameter
        $decisionAuthority = request()->route('decisionAuthority');
        if ($decisionAuthority && is_object($decisionAuthority)) {
            if (!$decisionAuthority->relationLoaded('board.categoryTerm')) {
                $decisionAuthority->load('board.categoryTerm');
            }
            
            if ($decisionAuthority->title) {
                $breadcrumbs[] = [
                    'label' => $decisionAuthority->title,
                    'url' => '#',
                    'icon' => null,
                    'current' => true,
                ];
            } else {
                $breadcrumbs[] = [
                    'label' => __('Decision Authority Details', 'fmr'),
                    'url' => '#',
                    'icon' => null,
                    'current' => true,
                ];
            }
        } else {
            $breadcrumbs[] = [
                'label' => __('Decision Authority Details', 'fmr'),
                'url' => '#',
                'icon' => null,
                'current' => true,
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Get breadcrumbs for search.
     * 
     * @return array
     */
    private function getSearchBreadcrumbs(): array
    {
        $breadcrumbs = [
            [
                'label' => __('Search', 'fmr'),
                'url' => route('search.show'),
                'icon' => 'heroicon-o-magnifying-glass',
                'current' => false,
            ]
        ];

        // Get the search query from URL parameters
        $searchQuery = request()->get('q');
        if ($searchQuery) {
            $breadcrumbs[] = [
                'label' => '"' . $searchQuery . '"',
                'url' => '#',
                'icon' => null,
                'current' => true,
            ];
        } else {
            $breadcrumbs[] = [
                'label' => __('Search Results', 'fmr'),
                'url' => '#',
                'icon' => null,
                'current' => true,
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Get breadcrumbs for styleguide.
     * 
     * @return array
     */
    private function getStyleguideBreadcrumbs(): array
    {
        return [
            [
                'label' => __('Style Guide', 'fmr'),
                'url' => route('styleguide'),
                'icon' => 'heroicon-o-paint-brush',
                'current' => true,
            ]
        ];
    }
}
