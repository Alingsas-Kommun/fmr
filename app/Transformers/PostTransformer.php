<?php

namespace App\Transformers;

use App\Models\Post;
use App\Services\FieldGroupService;
use Illuminate\Support\Str;

class PostTransformer
{
    protected $post;
    protected $rawMeta;
    protected $relations = [];
    
    // Core properties
    public readonly int $id;
    public readonly string $name;
    public readonly string $url;
    public readonly string $date;
    public readonly ?string $image;
    public readonly object $meta;
    
    // Relation properties (dynamically added if relations are loaded)
    // Note: These are set in constructor only if relations exist

    public function __construct(Post $post)
    {
        $this->post = $post;
        $this->rawMeta = $post->getLoadedMetaWithVisibility();

        // Set core properties
        $this->id = $this->post->ID;
        $this->name = $this->post->post_title;
        $this->url = get_permalink($this->post->ID);
        $this->date = $this->post->post_date;

        // Set image property
        $thumbnailId = $this->rawMeta['_thumbnail_id'] ?? null;
        if ($thumbnailId) {
            $this->image = wp_get_attachment_image_url($thumbnailId, 'full');
        } else {
            $this->image = null;
        }

        // Set relation properties if loaded (stored in internal array)
        $this->relations = [];
        
        $partyData = $this->getPartyData();
        if ($partyData !== null) {
            $this->relations['party'] = $partyData;
        }
        
        $categoryData = $this->getCategoryData();
        if ($categoryData !== null) {
            $this->relations['category'] = $categoryData;
        }

        // Create meta object with cleaned properties
        $this->meta = $this->buildMetaObject();
    }

    /**
     * Build meta object with cleaned properties
     */
    protected function buildMetaObject(): object
    {
        $metaData = FieldGroupService::formatMetaValues($this->rawMeta, $this->post->post_type);
        
        $camelCaseData = [];

        foreach ($metaData as $key => $value) {
            $camelCaseData[Str::camel($key)] = $value;
        }
        
        return (object) $camelCaseData;
    }

    /**
     * Get party data if relation is loaded
     */
    protected function getPartyData(): ?object
    {
        if ($this->post->relationLoaded('party') && $this->post->party) {            
            return $this->post->party->format();
        }

        return null;
    }

    /**
     * Get category data if relation is loaded
     */
    protected function getCategoryData(): ?string
    {
        if ($this->post->relationLoaded('categoryTerm') && $this->post->categoryTerm) {
            return $this->post->categoryTerm->name;
        }
        return null;
    }

    /**
     * Get the image for the post
     */
    public function image(string $size = 'thumbnail', string $class = ''): ?string
    {
        $thumbnailId = $this->rawMeta['_thumbnail_id'] ?? null;
        
        if (!$thumbnailId) {
            return null;
        }
        
        return wp_get_attachment_image($thumbnailId, $size, false, [
            'class' => $class
        ]);
    }

    /**
     * Get the edit URL for the post
     */

     public function editUrl(): string
    {
        return get_edit_post_link($this->id);
    }

    // Conversion methods
    public function toArray(bool $includeMeta = false): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'date' => $this->date,
            'image' => $this->image,
        ];

        // Add relation properties if they exist
        if (isset($this->relations['party'])) {
            $data['party'] = [
                'id' => $this->relations['party']->id,
                'name' => $this->relations['party']->name,
            ];
        }
        if (isset($this->relations['category'])) {
            $data['category'] = $this->relations['category'];
        }

        // Add extracted fields to first level
        $data = array_merge($data, $this->getExtractedData());

        if ($includeMeta) {
            $data['meta'] = $this->getCleanMeta(filterByExpectedFields: true);
        }

        return $data;
    }

    public function getCleanMeta(bool $filterByExpectedFields = true): array
    {
        $extractedFields = $this->getExtractedFields($this->post->post_type);
        
        if ($filterByExpectedFields) {
            $expectedFields = FieldGroupService::getExpectedFieldsForPostType($this->post->post_type);
            
            $allowedKeys = array_merge(
                array_keys($expectedFields),
                ['_thumbnail_id']
            );
            
            $filteredMeta = array_filter($this->rawMeta, function ($key) use ($extractedFields, $allowedKeys) {
                return in_array($key, $allowedKeys) && 
                       !in_array($key, $extractedFields) &&
                       !str_ends_with($key, '_visibility');
            }, ARRAY_FILTER_USE_KEY);
        } else {
            $systemKeys = ['_edit_lock', '_edit_last', '_thumbnail_id'];
            
            $filteredMeta = array_filter($this->rawMeta, function ($key) use ($systemKeys, $extractedFields) {
                return !in_array($key, $systemKeys) && 
                       !in_array($key, $extractedFields) &&
                       !str_ends_with($key, '_visibility');
            }, ARRAY_FILTER_USE_KEY);
        }

        return FieldGroupService::formatMetaValues($filteredMeta, $this->post->post_type, $extractedFields);
    }


    // Helper methods
    protected function getExtractedFields(string $postType): array
    {
        $expectedFields = FieldGroupService::getExpectedFieldsForPostType($postType);
        
        // Define which fields should be extracted to first level for each post type
        $extractedFieldIds = match($postType) {
            'person' => ['person_firstname', 'person_lastname', 'person_group_leader', 'person_active'],
            'party' => ['party_description', 'party_shortening', 'party_group_leader'],
            'board' => ['board_shortening'],
            default => []
        };
        
        // Return only fields that exist in the field group definitions
        return array_intersect($extractedFieldIds, array_keys($expectedFields));
    }

    protected function getExtractedData(): array
    {
        $data = [];

        $extractedFields = $this->getExtractedFields($this->post->post_type);
        $expectedFields = FieldGroupService::getExpectedFieldsForPostType($this->post->post_type);
        $relationFields = FieldGroupService::getRelationFieldIds($this->post->post_type);
        
        foreach ($extractedFields as $field) {
            // Skip relation fields as they're handled by relation properties
            if (in_array($field, $relationFields)) {
                continue;
            }
            
            $cleanKey = FieldGroupService::removePrefix($field, $this->post->post_type);
            $value = $this->rawMeta[$field] ?? null;
            
            // Get field type information for proper casting
            $fieldDefinition = $expectedFields[$field] ?? null;
            $fieldType = $fieldDefinition['type'] ?? 'text';
            
            $data[$cleanKey] = FieldGroupService::castValueByType($value, $fieldType);
        }
        
        return $data;
    }

    /**
     * Magic method to check if a dynamic property exists
     */
    public function __isset($name)
    {
        return isset($this->relations[$name]);
    }

    /**
     * Magic method to get dynamic properties
     */
    public function __get($name)
    {
        return $this->relations[$name] ?? null;
    }
}
