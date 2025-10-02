<?php

namespace App\Services;

use App\Models\Post;

class PostTransformService
{
    /**
     * Transform a single post to clean format
     */
    public function transform(Post $post, bool $includeMeta = false): array
    {
        $data = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'url' => get_permalink($post->ID),
            'date' => $post->post_date,
        ];

        // Add image if available
        $meta = $post->getLoadedMetaWithVisibility();
        $thumbnailId = $meta['_thumbnail_id'] ?? null;
        if ($thumbnailId) {
            $data['image'] = wp_get_attachment_image_url($thumbnailId, 'full');
        }

        // Add type-specific data
        $data = array_merge($data, $this->getTypeSpecificData($post));

        // Add meta fields if requested
        if ($includeMeta) {
            $data['meta'] = $this->getCleanMeta($post);
        }

        return $data;
    }

    /**
     * Transform a collection of posts
     */
    public function transformCollection($posts, bool $includeMeta = false): array
    {
        return $posts->map(fn($post) => $this->transform($post, $includeMeta))->toArray();
    }

    /**
     * Get type-specific data based on post_type
     */
    private function getTypeSpecificData(Post $post): array
    {
        return match($post->post_type) {
            'person' => $this->getPersonData($post),
            'party' => $this->getPartyData($post),
            'board' => $this->getBoardData($post),
            default => []
        };
    }

    /**
     * Get person-specific data
     */
    private function getPersonData(Post $post): array
    {
        $meta = $post->getLoadedMetaWithVisibility();
        
        return [
            'firstname' => $meta['person_firstname'] ?? null,
            'lastname' => $meta['person_lastname'] ?? null,
            'is_active' => $this->convertToBoolean($meta['person_active'] ?? null),
            'is_group_leader' => $this->convertToBoolean($meta['person_group_leader'] ?? null),
            'party' => $post->party ? [
                'id' => $post->party->ID,
                'name' => $post->party->post_title,
            ] : null,
        ];
    }

    /**
     * Get party-specific data
     */
    private function getPartyData(Post $post): array
    {
        $meta = $post->getLoadedMetaWithVisibility();
        
        return [
            'description' => $meta['party_description'] ?? null,
            'shortening' => $meta['party_shortening'] ?? null,
            'group_leader' => $meta['party_group_leader'] ?? null,
        ];
    }

    /**
     * Get board-specific data
     */
    private function getBoardData(Post $post): array
    {
        $meta = $post->getLoadedMetaWithVisibility();
        
        return [
            'shortening' => $meta['board_shortening'] ?? null,
            'category' => $post->categoryTerm?->name,
        ];
    }

    /**
     * Get clean meta values for format (excluding extracted fields)
     */
    private function getCleanMeta(Post $post): array
    {
        $systemKeys = ['_edit_lock', '_edit_last', '_thumbnail_id'];
        $relationKeys = ['person_party', 'board_category'];
        $extractedFields = $this->getExtractedFields($post->post_type);
        
        $meta = $post->getLoadedMetaWithVisibility();
        
        // Filter out unwanted keys
        $filteredMeta = array_filter($meta, function ($key) use ($systemKeys, $relationKeys, $extractedFields) {
            return !in_array($key, $systemKeys) && 
                   !in_array($key, $relationKeys) && 
                   !in_array($key, $extractedFields) &&
                   !str_ends_with($key, '_visibility');
        }, ARRAY_FILTER_USE_KEY);

        return $this->cleanMetaValues($filteredMeta);
    }

    /**
     * Get extracted fields based on post type
     */
    private function getExtractedFields(string $postType): array
    {
        return match($postType) {
            'person' => ['person_firstname', 'person_lastname', 'person_group_leader', 'person_active'],
            'party' => ['party_description', 'party_shortening', 'party_group_leader'],
            'board' => ['board_shortening'],
            default => []
        };
    }

    /**
     * Clean meta values for format (remove prefixes, convert booleans)
     */
    private function cleanMetaValues(array $metaValues): array
    {
        $cleaned = [];
        
        foreach ($metaValues as $key => $value) {
            $cleanKey = $this->removePrefixes($key);
            $cleanValue = $this->convertToBoolean($value);
            $cleaned[$cleanKey] = $cleanValue;
        }
        
        return $cleaned;
    }

    /**
     * Remove prefixes from meta keys
     */
    private function removePrefixes(string $key): string
    {
        $prefixes = ['person_', 'party_', 'board_'];
        
        foreach ($prefixes as $prefix) {
            if (str_starts_with($key, $prefix)) {
                return substr($key, strlen($prefix));
            }
        }
        
        return $key;
    }

    /**
     * Convert string '1'/'0' to boolean
     */
    private function convertToBoolean($value)
    {
        if ($value === null) return null;
        if ($value === '1') return true;
        if ($value === '0') return false;

        return $value;
    }
}
