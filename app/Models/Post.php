<?php

namespace App\Models;

use function App\Core\getImageElement;

use Illuminate\Database\Eloquent\Model;
use App\Database\Eloquent\Relations\BelongsToMeta;

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
            ->with('board', 'roleTerm')
            ->orderBy('period_start', 'desc');
    }

    /**
     * Get active assignments
     */
    public function activeAssignments()
    {
        return $this->personAssignments()->active();
    }

    /**
     * Get the category term for boards (meta-based relationship)
     */
    public function categoryTerm()
    {
        return $this->belongsToMeta(Term::class, 'board_category', 'term_id', 'categoryTerm');
    }

    /**
     * Get the party for persons (meta-based relationship)
     */
    public function party()
    {
        return $this->belongsToMeta(Post::class, 'person_party', 'ID', 'party');
    }

    /**
     * Custom relationship method for meta-based belongsTo relationships
     */
    protected function belongsToMeta($related, $metaKey, $ownerKey = null, $relationName = null)
    {
        $instance = $this->newRelatedInstance($related);
        $ownerKey = $ownerKey ?: $instance->getKeyName();
        $relationName = $relationName ?: 'belongsToMeta';
        
        return new BelongsToMeta(
            $instance->newQuery(),
            $this,
            $metaKey,
            $ownerKey,
            $relationName
        );
    }

    /**
     * Get the post type instance
     */
    public static function type($type)
    {
        return static::where('post_type', $type);
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
        
        // If user is logged out and visibility is false, return default
        if (!$visibility && !is_user_logged_in()) {
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
     * Get multiple meta values by keys with visibility check
     *
     * @param array $keys
     * @return array
     */
    public function getMetaValues(array $keys)
    {
        // Add visibility keys
        $visibilityKeys = array_map(function($key) {
            return $key . '_visibility';
        }, $keys);

        // Get all meta values in a single query
        $allMetaKeys = array_merge($keys, $visibilityKeys);
        $metaValues = $this->meta()
            ->whereIn('meta_key', $allMetaKeys)
            ->pluck('meta_value', 'meta_key')
            ->toArray();

        // Process the results and check visibility
        $result = [];
        
        foreach ($keys as $key) {
            $result[$key] = $this->processMetaValueWithVisibility($key, $metaValues);
        }

        return $result;
    }

    /**
     * Process a single meta value with visibility check for logged out users
     *
     * @param string $key
     * @param array $metaValues
     * @return mixed
     */
    private function processMetaValueWithVisibility($key, $metaValues)
    {
        $visibilityKey = $key . '_visibility';
        $isVisible = $metaValues[$visibilityKey] ?? true;
        
        // If user is logged out and visibility is false, return null
        if (!$isVisible && !is_user_logged_in()) {
            return null;
        }
        
        return $metaValues[$key] ?? null;
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
    public function scopeType($query, $type)
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

    public function scopeActiveAssignments($query)
    {
        return $query->whereHas('personAssignments', function($query) {
            $query->active();
        });
    }

    public function scopeInactiveAssignments($query)
    {
        return $query->whereDoesntHave('personAssignments', function($query) {
            $query->active();
        });
    }
}