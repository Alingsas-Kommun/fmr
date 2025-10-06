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
        'author_id',
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

    /**
     * Get the author (user) associated with the decision authority.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'ID');
    }

    /**
     * Scope a query to only include active decision authorities (ongoing).
     */
    public function scopeActive($query)
    {
        $today = now();
        
        return $query->where(function($q) use ($today) {
            $q->where('start_date', '<=', $today)
                ->where(function($q) use ($today) {
                    $q->where('end_date', '>=', $today)
                        ->orWhereNull('end_date');
                });
        });
    }

    /**
     * Scope a query to only include inactive decision authorities (past).
     */
    public function scopeInactive($query)
    {
        $today = now();
        
        return $query->where('end_date', '<', $today);
    }

    /**
     * Scope a query to only include decision authorities that are currently ongoing.
     * This is an alias for the active scope for better readability.
     */
    public function scopeOngoing($query)
    {
        return $query->active();
    }

    /**
     * Scope a query to only include decision authorities that have ended.
     * This is an alias for the inactive scope for better readability.
     */
    public function scopePast($query)
    {
        return $query->inactive();
    }

    /**
     * Transform this decision authority to clean API format
     */
    public function toApiFormat(): array
    {
        $data = $this->toArray();
        
        // Remove unwanted fields
        unset($data['author_id']);
        unset($data['type_term_id']);
        unset($data['type_term']);
        unset($data['created_at']);
        unset($data['updated_at']);
        
        $data['type'] = $this->typeTerm?->name;
        
        return $data;
    }

    /**
     * Transform a collection of decision authorities to API format
     */
    public static function toApiCollection($decisionAuthorities): array
    {
        return $decisionAuthorities->map(fn($da) => $da->toApiFormat())->toArray();
    }

    /**
     * Check if this decision authority is currently active.
     */
    public function isActive(): bool
    {
        $today = now();
        
        return $this->start_date <= $today && 
               ($this->end_date >= $today || $this->end_date === null);
    }
}
