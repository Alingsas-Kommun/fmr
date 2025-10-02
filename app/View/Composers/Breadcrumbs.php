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
            'breadcrumbs' => $this->buildBreadcrumbs(),
        ];
    }

    /**
     * Build breadcrumbs by merging from different sources.
     */
    public function buildBreadcrumbs(): array
    {
        $currentRoute = Route::currentRouteName();
        $routeBreadcrumbs = $this->getRouteBreadcrumbs($currentRoute);
        
        // If no route breadcrumbs and we're on homepage route, skip
        if (empty($routeBreadcrumbs) && ($currentRoute === 'homepage' || $currentRoute === null)) {
            return [];
        }

        $breadcrumbs = [
            [
                'label' => __('Home', 'fmr'),
                'url' => home_url('/'), // @phpstan-ignore-line
                'icon' => 'heroicon-o-home',
                'current' => false,
            ]
        ];
        
        $breadcrumbs = array_merge($breadcrumbs, $routeBreadcrumbs);

        return $breadcrumbs;
    }

    /**
     * Get breadcrumbs for specific routes.
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
            case 'assignments.index':
                return $this->getAssignmentsIndexBreadcrumbs();
            case 'assignments.show':
                return $this->getAssignmentsShowBreadcrumbs();
            case 'decision-authorities.index':
                return $this->getDecisionAuthoritiesIndexBreadcrumbs();
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
     * Get breadcrumbs for assignments index.
     */
    private function getAssignmentsIndexBreadcrumbs(): array
    {
        return [
            [
                'label' => __('Assignments', 'fmr'),
                'url' => route('assignments.index'),
                'icon' => 'heroicon-o-clipboard-document-list',
                'current' => true,
            ]
        ];
    }

    /**
     * Get breadcrumbs for assignments show.
     */
    private function getAssignmentsShowBreadcrumbs(): array
    {
        $breadcrumbs = [
            [
                'label' => __('Assignments', 'fmr'),
                'url' => route('assignments.index'),
                'icon' => 'heroicon-o-clipboard-document-list',
                'current' => false,
            ]
        ];

        // Get the assignment data from the route parameter
        $assignment = request()->route('assignment');
        if ($assignment) {
            // Load the roleTerm relationship if not already loaded
            if (!$assignment->relationLoaded('roleTerm')) {
                $assignment->load('roleTerm');
            }
            
            if ($assignment->roleTerm) {
                $breadcrumbs[] = [
                    'label' => $assignment->roleTerm->name,
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
     * Get breadcrumbs for decision authorities index.
     */
    private function getDecisionAuthoritiesIndexBreadcrumbs(): array
    {
        return [
            [
                'label' => __('Decision Authorities', 'fmr'),
                'url' => route('decision-authorities.index'),
                'icon' => 'heroicon-o-scale',
                'current' => true,
            ]
        ];
    }

    /**
     * Get breadcrumbs for decision authorities show.
     */
    private function getDecisionAuthoritiesShowBreadcrumbs(): array
    {
        $breadcrumbs = [
            [
                'label' => __('Decision Authorities', 'fmr'),
                'url' => route('decision-authorities.index'),
                'icon' => 'heroicon-o-scale',
                'current' => false,
            ]
        ];

        // Get the decision authority data from the route parameter
        $decisionAuthority = request()->route('decisionAuthority');
        if ($decisionAuthority) {
            // Load the typeTerm relationship if not already loaded
            if (!$decisionAuthority->relationLoaded('typeTerm')) {
                $decisionAuthority->load('typeTerm');
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
