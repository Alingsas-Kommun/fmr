<?php

namespace App\Models;

use function App\Core\getImageElement;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $connection = 'wordpress';

    protected $fillable = [
        'post_title',
        'post_content',
        'post_status',
        'post_type',
        'post_name',
        'post_date',
        'post_modified'
    ];

    /**
     * Get all post meta fields
     */
    public function meta()
    {
        return $this->hasMany(PostMeta::class, 'post_id', 'ID');
    }

    /**
     * Get assignments where this post is the person
     */
    public function personAssignments()
    {
        return $this->hasMany(Assignment::class, 'person_id', 'ID')
            ->with('board')
            ->orderBy('period_start', 'desc');
    }

    /**
     * Get assignments where this post is the board
     */
    public function boardAssignments()
    {
        return $this->hasMany(Assignment::class, 'board_id', 'ID')
            ->with('person')
            ->orderBy('role');
    }

    /**
     * Get active assignments
     */
    public function activeAssignments()
    {
        $relation = $this->post_type === 'board' ? 'boardAssignments' : 'personAssignments';
        return $this->$relation()
            ->where('period_start', '<=', now())
            ->where('period_end', '>=', now());
    }

    /**
     * Get the post type instance
     */
    public static function type($type)
    {
        return static::where('post_type', $type)
            ->where('post_status', 'publish');
    }

    /**
     * Get boards
     */
    public static function boards()
    {
        return static::type('board');
    }

    /**
     * Get persons
     */
    public static function persons()
    {
        return static::type('person');
    }

    /**
     * Get parties
     */
    public static function parties()
    {
        return static::type('party');
    }

    /**
     * Get the thumbnail ID for the post
     */
    public function thumbnail($class = '')
    {
        return getImageElement($this->getMeta('_thumbnail_id'), 'thumbnail', $class);
    }

    /**
     * Get a specific meta value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getMeta($key, $default = null)
    {
        $visibility = $this->getMetaVisibility($key);
        
        if (!$visibility) {
            return $default;
        }

        return $this->meta()
            ->where('meta_key', $key)
            ->value('meta_value') ?? $default;
    }

    public function getMetaVisibility($key)
    {
        return $this->meta()
            ->where('meta_key', $key . '_visibility')
            ->value('meta_value') ?? true;
    }

    /**
     * Set a meta value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setMeta($key, $value)
    {
        $this->meta()->updateOrCreate(
            ['meta_key' => $key],
            ['meta_value' => $value]
        );
    }

    /**
     * Get multiple meta values by keys
     *
     * @param array $keys
     * @return array
     */
    public function getMetaValues(array $keys)
    {
        return $this->meta()
            ->whereIn('meta_key', $keys)
            ->pluck('meta_value', 'meta_key')
            ->toArray();
    }

    /**
     * Scope a query to only include published posts
     */
    public function scopePublished($query)
    {
        return $query->where('post_status', 'publish');
    }

    /**
     * Scope a query to only include posts of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('post_type', $type);
    }

    /**
     * Scope a query to include posts with specific meta value
     */
    public function scopeWithMeta($query, $key, $value)
    {
        return $query->whereHas('meta', function ($query) use ($key, $value) {
            $query->where('meta_key', $key)
                  ->where('meta_value', $value);
        });
    }
}