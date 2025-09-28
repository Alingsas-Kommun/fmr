<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_login',
        'user_pass',
        'user_nicename',
        'user_email',
        'user_url',
        'user_registered',
        'user_activation_key',
        'user_status',
        'display_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_registered' => 'datetime',
        'user_status' => 'integer',
    ];

    /**
     * Get the assignments created by this user.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'author_id', 'ID');
    }

    /**
     * Get the decision authorities created by this user.
     */
    public function decisionAuthorities()
    {
        return $this->hasMany(DecisionAuthority::class, 'author_id', 'ID');
    }

    /**
     * Get the user's display name or login name.
     */
    public function getNameAttribute()
    {
        return $this->display_name ?: $this->user_login;
    }

    /**
     * Check if the user is active.
     */
    public function isActive()
    {
        return $this->user_status === 0;
    }
}
