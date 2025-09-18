<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assignments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'decision_authority_id',
        'person_id',
        'role_term_id',
        'period_start',
        'period_end',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the board associated with the assignment.
     */
    public function decisionAuthority()
    {
        return $this->belongsTo(DecisionAuthority::class);
    }

    /**
     * Get the board associated with the assignment through decision authority.
     */
    public function board()
    {
        return $this->hasOneThrough(
            Post::class,
            DecisionAuthority::class,
            'id', // Foreign key on decision_authority table
            'ID', // Foreign key on posts table
            'decision_authority_id', // Local key on assignments table
            'board_id' // Local key on decision_authority table
        )->where('post_type', 'board');
    }

    /**
     * Get the person associated with the assignment.
     */
    public function person()
    {
        return $this->belongsTo(Post::class, 'person_id', 'ID')->type('person');
    }

    /**
     * Get the role term associated with the assignment.
     */
    public function roleTerm()
    {
        return $this->belongsTo(Term::class, 'role_term_id', 'term_id');
    }

    /**
     * Get the role name through the term relationship.
     */
    public function getRoleAttribute()
    {
        return $this->roleTerm ? $this->roleTerm->name : null;
    }
}
