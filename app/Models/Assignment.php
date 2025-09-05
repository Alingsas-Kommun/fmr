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
        'board_id',
        'person_id',
        'role',
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
    public function board()
    {
        return $this->belongsTo(Post::class, 'board_id', 'ID')->where('post_type', 'board');
    }

    /**
     * Get the person associated with the assignment.
     */
    public function person()
    {
        return $this->belongsTo(Post::class, 'person_id', 'ID')->where('post_type', 'person');
    }
}
