<?php

namespace App\Collections;

use App\Transformers\PostTransformer;
use Illuminate\Database\Eloquent\Collection;

class PostCollection extends Collection
{
    /**
     * Format all posts in this collection using the decorator
     * Returns collection of PostTransformer objects for use in views
     */
    public function format(bool $includeMeta = false): PostCollection
    {
        return new PostCollection($this->map(fn($post) => new PostTransformer($post))->values()->toArray($includeMeta));
    }

    /**
     * Format all posts in this collection for API responses
     * Returns array of arrays for JSON responses
     */
    public function apiFormat(bool $includeMeta = false): array
    {
        return $this->map(fn($post) => new PostTransformer($post)->toArray($includeMeta))->values()->toArray();
    }
}