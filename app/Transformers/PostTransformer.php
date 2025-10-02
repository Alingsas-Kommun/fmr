<?php

namespace App\Transformers;

use App\Models\Post;

class PostTransformer
{
    protected $post;
    protected $rawMeta;
    
    // Core properties
    public readonly int $id;
    public readonly string $name;
    public readonly string $url;
    public readonly string $date;
    public readonly ?string $image;
    public readonly object $meta;
    
    // Relation properties (only set if loaded)
    public readonly ?object $party;
    public readonly ?string $category;

    public function __construct(Post $post)
    {
        $this->post = $post;
        $this->rawMeta = $post->getLoadedMetaWithVisibility();

        // Set core properties
        $this->id = $this->post->ID;
        $this->name = $this->post->post_title;
        $this->url = \get_permalink($this->post->ID);
        $this->date = $this->post->post_date;

        // Set image property
        $thumbnailId = $this->rawMeta['_thumbnail_id'] ?? null;
        if ($thumbnailId) {
            $this->image = \wp_get_attachment_image_url($thumbnailId, 'full');
        } else {
            $this->image = null;
        }

        // Set relation properties if loaded
        $this->party = $this->getPartyData();
        $this->category = $this->getCategoryData();

        // Create meta object with cleaned properties
        $this->meta = $this->buildMetaObject();
    }

    /**
     * Build meta object with cleaned properties
     */
    protected function buildMetaObject(): object
    {
        $metaData = [];
        
        foreach ($this->rawMeta as $key => $value) {
            // Skip system keys
            if (in_array($key, ['_edit_lock', '_edit_last', '_thumbnail_id'])) {
                continue;
            }
            
            // Remove prefixes and visibility keys
            if (str_ends_with($key, '_visibility')) {
                continue;
            }
            
            $cleanKey = $this->removePrefix($key);
            $cleanValue = $this->convertToBoolean($value);
            
            $metaData[$cleanKey] = $cleanValue;
        }
        
        return (object) $metaData;
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

    // Meta methods
    public function getMeta(): array
    {
        return $this->rawMeta;
    }

    public function getCleanMeta(): array
    {
        $systemKeys = ['_edit_lock', '_edit_last', '_thumbnail_id'];
        $relationKeys = ['person_party', 'board_category'];
        $extractedFields = $this->getExtractedFields($this->post->post_type);
        
        $filteredMeta = array_filter($this->rawMeta, function ($key) use ($systemKeys, $relationKeys, $extractedFields) {
            return !in_array($key, $systemKeys) && 
                   !in_array($key, $relationKeys) && 
                   !in_array($key, $extractedFields) &&
                   !str_ends_with($key, '_visibility');
        }, ARRAY_FILTER_USE_KEY);

        return $this->cleanMetaValues($filteredMeta);
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
        if ($this->party !== null) {
            $data['party'] = [
                'id' => $this->party->id,
                'name' => $this->party->name,
            ];
        }
        if ($this->category !== null) {
            $data['category'] = $this->category;
        }

        // Add extracted fields to first level
        $data = array_merge($data, $this->getExtractedData());

        if ($includeMeta) {
            $data['meta'] = $this->getCleanMeta();
        }

        return $data;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function getOriginal(): Post
    {
        return $this->post;
    }

    // Magic methods for property access
    public function __get($key)
    {
        return match($key) {
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'date' => $this->date,
            'image' => $this->image,
            'meta' => $this->meta,
            'party' => $this->party,
            'category' => $this->category,
            default => null
        };
    }

    public function __isset($key)
    {
        return in_array($key, ['id', 'name', 'url', 'date', 'image', 'meta', 'party', 'category']);
    }

    // Helper methods
    protected function getExtractedFields(string $postType): array
    {
        return match($postType) {
            'person' => ['person_firstname', 'person_lastname', 'person_group_leader', 'person_active'],
            'party' => ['party_description', 'party_shortening', 'party_group_leader'],
            'board' => ['board_shortening', 'board_category'],
            default => []
        };
    }

    protected function getExtractedData(): array
    {
        $extractedFields = $this->getExtractedFields($this->post->post_type);
        $data = [];
        
        foreach ($extractedFields as $field) {
            // Skip relation fields as they're handled by relation properties
            if (in_array($field, ['person_party', 'board_category'])) {
                continue;
            }
            
            $cleanKey = $this->removePrefix($field);
            $value = $this->rawMeta[$field] ?? null;
            $data[$cleanKey] = $this->convertToBoolean($value);
        }
        
        return $data;
    }

    protected function cleanMetaValues(array $metaValues): array
    {
        $cleaned = [];
        
        foreach ($metaValues as $key => $value) {
            $cleanKey = $this->removePrefix($key);
            $cleanValue = $this->convertToBoolean($value);
            $cleaned[$cleanKey] = $cleanValue;
        }
        
        return $cleaned;
    }

    protected function removePrefix(string $key): string
    {
        $prefixes = ['person_', 'party_', 'board_'];
        
        foreach ($prefixes as $prefix) {
            if (str_starts_with($key, $prefix)) {
                return substr($key, strlen($prefix));
            }
        }
        
        return $key;
    }

    protected function convertToBoolean($value)
    {
        if ($value === null) return null;
        if ($value === '1') return true;
        if ($value === '0') return false;

        return $value;
    }
}
