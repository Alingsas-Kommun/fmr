<?php

namespace App\View\Composers;

use App\Models\Post;
use App\Models\DecisionAuthority;
use Roots\Acorn\View\Composer;

class Board extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.post-types.content-single-board',
        'partials.post-types.content-board',
    ];


    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'board' => $this->board(),
            'decisionAuthorities' => $this->decisionAuthorities(),
        ];
    }

    /**
     * Retrieve the board object with formatted meta fields.
     */
    public function board()
    {
        $boardId = get_the_ID(); // @phpstan-ignore-line
        
        if (!$boardId) {
            return null;
        }

        return Post::with(['meta', 'categoryTerm'])
            ->find($boardId)
            ?->format();
    }

    /**
     * Get decision authorities for the board.
     */
    public function decisionAuthorities()
    {
        $boardId = get_the_ID(); // @phpstan-ignore-line
        
        if (!$boardId) {
            return collect();
        }

        $decisionAuthorities = DecisionAuthority::where('board_id', $boardId)
            ->with('typeTerm')
            ->orderBy('start_date', 'desc')
            ->active()
            ->get();

        return $decisionAuthorities->map(function ($authority) {
            return (object) [
                'id' => $authority->id,
                'title' => [
                    'url' => route('decision-authorities.show', $authority),
                    'text' => $authority->title,
                ],
                'type' => $authority->typeTerm->name,
                'period' => $authority->start_date->format('Y-m-d') . ' - ' . $authority->end_date->format('Y-m-d'),
                'view' => [
                    'url' => route('decision-authorities.show', $authority),
                    'text' => __('View', 'fmr'),
                ]
            ];
        })->toArray();
    }
}