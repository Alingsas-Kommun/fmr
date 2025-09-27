<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DecisionAuthority extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'decision_authority';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'board_id',
        'title',
        'type_term_id',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the board associated with the decision authority.
     */
    public function board()
    {
        return $this->belongsTo(Post::class, 'board_id', 'ID')->type('board');
    }

    /**
     * Get the assignments associated with this decision authority.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the type term associated with the decision authority.
     */
    public function typeTerm()
    {
        return $this->belongsTo(Term::class, 'type_term_id', 'term_id');
    }
}
