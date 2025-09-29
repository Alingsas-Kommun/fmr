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
        'author_id',
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

    /**
     * Get the author (user) associated with the assignment.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'ID');
    }

    /**
     * Scope a query to only include active assignments (ongoing).
     */
    public function scopeActive($query)
    {
        $today = now();
        
        return $query->where(function($q) use ($today) {
            $q->where('period_start', '<=', $today)
                ->where(function($q) use ($today) {
                    $q->where('period_end', '>=', $today)
                        ->orWhereNull('period_end');
                });
        });
    }

    /**
     * Scope a query to only include inactive assignments (past).
     */
    public function scopeInactive($query)
    {
        $today = now();
        
        return $query->where('period_end', '<', $today);
    }

    /**
     * Scope a query to only include assignments that are currently ongoing.
     * This is an alias for the active scope for better readability.
     */
    public function scopeOngoing($query)
    {
        return $query->active();
    }

    /**
     * Scope a query to only include assignments that have ended.
     * This is an alias for the inactive scope for better readability.
     */
    public function scopePast($query)
    {
        return $query->inactive();
    }
}
