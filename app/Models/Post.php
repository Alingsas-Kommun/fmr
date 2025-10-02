<?php

namespace App\Models;

use App\Services\PostTransformService;

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
        $value = $this->meta()
            ->where('meta_key', $key)
            ->value('meta_value') ?? $default;
            
        $visibleValue = $this->checkMetaVisibility($key, $value);
        
        return $visibleValue !== null ? $visibleValue : $default;
    }

    public function getMetaVisibility($key)
    {
        return $this->meta()
            ->where('meta_key', $key . '_visibility')
            ->value('meta_value') ?? true;
    }

    /**
     * Check if a meta value should be visible based on visibility settings
     * 
     * @param string $key The meta key
     * @param mixed $value The meta value
     * @param array $metaValues Array of all meta values (for performance)
     * @return mixed|null Returns the value if visible, null if hidden
     */
    private function checkMetaVisibility($key, $value, $metaValues = null)
    {
        $visibilityKey = $key . '_visibility';
        $isVisible = true;
        
        if ($metaValues !== null) {
            $isVisible = $metaValues[$visibilityKey] ?? true;
        } else {
            $isVisible = $this->getMetaVisibility($key);
        }
        
        if (!$isVisible && !\is_user_logged_in()) {
            return null;
        }
        
        return $value;
    }

    /**
     * Get multiple meta values by keys with visibility check
     *
     * @param array $keys
     * @return array
     */
    public function getMetaValues(array $keys)
    {
        $visibilityKeys = array_map(function($key) {
            return $key . '_visibility';
        }, $keys);

        $allMetaKeys = array_merge($keys, $visibilityKeys);
        $metaValues = $this->meta()
            ->whereIn('meta_key', $allMetaKeys)
            ->pluck('meta_value', 'meta_key')
            ->toArray();

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
        $value = $metaValues[$key] ?? null;

        return $this->checkMetaVisibility($key, $value, $metaValues);
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

    /**
     * Get loaded meta data with visibility checks applied
     * This method uses already loaded meta relationship for better performance
     */
    public function getLoadedMetaWithVisibility(): array
    {
        if (!$this->relationLoaded('meta')) {
            return [];
        }

        $meta = [];
        $metaValues = [];
        
        foreach ($this->meta as $metaItem) {
            $metaValues[$metaItem->meta_key] = $metaItem->meta_value;
        }

        foreach ($metaValues as $key => $value) {
            if (str_ends_with($key, '_visibility')) {
                continue;
            }
            
            $meta[$key] = $this->checkMetaVisibility($key, $value, $metaValues);
        }

        return $meta;
    }

    /**
     * Format this post using the transform service
     */
    public function format(bool $includeMeta = false): array
    {
        $service = app(PostTransformService::class);
        
        return $service->transform($this, $includeMeta);
    }

    /**
     * Format a collection of posts using the transform service
     */
    public static function formatCollection($posts, bool $includeMeta = false): array
    {
        $service = app(PostTransformService::class);

        return $service->transformCollection($posts, $includeMeta);
    }
}