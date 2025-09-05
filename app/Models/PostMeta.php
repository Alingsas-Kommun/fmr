<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'postmeta';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'meta_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'wordpress';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'post_id',
        'meta_key',
        'meta_value'
    ];

    /**
     * Get the post that owns the meta.
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'ID');
    }

    /**
     * Scope a query to only include meta with a specific key.
     */
    public function scopeWithKey($query, $key)
    {
        return $query->where('meta_key', $key);
    }
}
