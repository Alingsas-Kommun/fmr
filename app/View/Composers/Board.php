<?php

namespace App\View\Composers;

use App\Models\Post;
use App\Models\DecisionAuthority;
use App\Models\Term;
use Roots\Acorn\View\Composer;
use Illuminate\Support\Str;

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
     * List of meta fields to be passed via the board object.
     *
     * @var array
     */
    protected static $metaFields = [
        'board_category',
        'board_shortening',
        'board_address',
        'board_visiting_address',
        'board_zip',
        'board_city',
        'board_website',
        'board_email',
        'board_phone',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'board' => $this->boardWithMeta(),
            'decisionAuthorities' => $this->decisionAuthorities(),
        ];
    }

    /**
     * Retrieve the board object.
     */
    public function board()
    {
        $boardId = get_the_ID(); // @phpstan-ignore-line
        
        if (!$boardId) {
            return null;
        }

        return Post::find($boardId);
    }

    /**
     * Retrieve the board object with aggregated meta fields.
     */
    public function boardWithMeta()
    {
        $board = $this->board();
        
        if (!$board) {
            return null;
        }

        $metaValues = $this->boardMeta();
        
        foreach ($metaValues as $key => $value) {
            $propertyName = Str::camel(Str::replace('board_', '', $key)); 
            
            // Handle taxonomy relations - convert term IDs to term names
            if ($key === 'board_category' && $value) {
                $term = Term::find($value);
                $board->$propertyName =$term;
            }
        }

        return $board;
    }

    /**
     * Get decision authorities for the board.
     */
    public function decisionAuthorities()
    {
        $board = $this->board();
        
        if (!$board) {
            return collect();
        }

        return DecisionAuthority::where('board_id', $board->ID)
            ->with('typeTerm')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Get all visible board meta fields in a single query.
     */
    public function boardMeta()
    {
        $board = $this->board();
        
        if (!$board) {
            return [];
        }

        return $board->getMetaValues(static::$metaFields);
    }
}