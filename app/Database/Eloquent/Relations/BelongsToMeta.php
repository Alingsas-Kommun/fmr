<?php

namespace App\Database\Eloquent\Relations;

use App\Models\PostMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BelongsToMeta extends BelongsTo
{
    /**
     * The meta key to use for the relationship
     */
    protected $metaKey;

    /**
     * Create a new belongs to meta relationship instance.
     */
    public function __construct(Builder $query, Model $child, $metaKey, $ownerKey, $relationName)
    {
        $this->metaKey = $metaKey;
        
        // We'll use a dummy foreign key since we're handling the relationship differently
        parent::__construct($query, $child, 'dummy_key', $ownerKey, $relationName);
    }

    /**
     * Set the base constraints on the relation query.
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            // Get the meta value for the parent model using the meta relationship
            $metaValue = $this->getMetaValue($this->child);
            
            if ($metaValue) {
                $this->query->where($this->ownerKey, $metaValue);
            } else {
                // If no meta value, return empty result
                $this->query->whereRaw('0 = 1');
            }
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     */
    public function addEagerConstraints(array $models)
    {
        // Get all meta values using PostMeta relationship
        $metaValues = $this->getMetaValuesForModels($models);

        if (!empty($metaValues)) {
            $this->query->whereIn($this->ownerKey, $metaValues);
        } else {
            $this->query->whereRaw('0 = 1');
        }
    }

    /**
     * Match the eagerly loaded results to their parents.
     */
    public function match(array $models, Collection $results, $relation)
    {
        // Create a dictionary of results keyed by the owner key
        $dictionary = $results->keyBy($this->ownerKey);

        // Match each model with its related result using meta values
        foreach ($models as $model) {
            $metaValue = $this->getMetaValue($model);
            
            if ($metaValue && $dictionary->has($metaValue)) {
                $model->setRelation($relation, $dictionary->get($metaValue));
            }
        }

        return $models;
    }

    /**
     * Get meta value for a single model
     */
    protected function getMetaValue($model)
    {
        // Check if meta relationship is already loaded
        if ($model->relationLoaded('meta')) {
            $metaRecord = $model->meta->where('meta_key', $this->metaKey)->first();
            return $metaRecord ? $metaRecord->meta_value : null;
        }
        
        // Fallback to getMeta method
        return $model->getMeta($this->metaKey);
    }

    /**
     * Get meta values for multiple models efficiently
     */
    protected function getMetaValuesForModels(array $models)
    {
        $postIds = collect($models)->pluck('ID')->all();
        
        // Query PostMeta directly for better performance
        $metaRecords = PostMeta::whereIn('post_id', $postIds)
            ->where('meta_key', $this->metaKey)
            ->get()
            ->keyBy('post_id');

        // Extract unique meta values
        return $metaRecords->pluck('meta_value')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
