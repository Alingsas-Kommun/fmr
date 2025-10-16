<?php

namespace App\Utilities;

use Exception;
use Illuminate\Support\Facades\Blade;

class TableColumn
{
    public static function make(string $key, ?string $label = null, array $options = []): array
    {
        return array_merge([
            'key' => $key,
            'label' => $label ?? ucfirst(str_replace(['_', '.'], ' ', $key)),
        ], $options);
    }

    public static function text(string $key, ?string $label = null, string $class = ''): array
    {
        return self::make($key, $label, [
            'render' => function ($item) use ($key, $class) {
                $value = data_get($item, $key);
                return Blade::render('<span class="' . $class . '">{{ $value }}</span>', ['value' => $value]);
            }
        ]);
    }

    public static function link(string $key, ?string $label = null, string $urlKey = 'url', string $class = ''): array
    {
        return self::make($key, $label, [
            'render' => function ($item) use ($key, $urlKey, $class) {
                $url = data_get($item, $urlKey, '#');
                $value = data_get($item, $key);
                
                return Blade::render(
                    '<x-link href="{{ $url }}" class="!inline-block ' . $class . '">{{ $value }}</x-link>',
                    ['url' => $url, 'value' => $value]
                );
            }
        ]);
    }

    public static function arrowLink(string $key, ?string $label = null, string $urlKey = 'url', string $class = ''): array
    {
        return self::make($key, $label, [
            'render' => function ($item) use ($key, $urlKey, $class) {
                $url = data_get($item, $urlKey, '#');
                $value = data_get($item, $key);
                
                return Blade::render('
                    <x-link href="{{ $url }}" class="' . $class . ' flex items-center space-x-1">
                        <span>{{ $value }}</span>
                        <x-heroicon-o-arrow-right class="h-4 w-4 mr-1" />
                    </x-link>',
                    ['url' => $url, 'value' => $value]
                );
            }
        ]);
    }

    public static function imageLink(string $key, ?string $label = null, string $urlKey = 'url', string $imageKey = 'image', string $class = 'text-blue-600 hover:text-blue-800'): array
    {
        return self::make($key, $label, [
            'render' => function ($item) use ($key, $urlKey, $imageKey, $class) {
                $url = data_get($item, $urlKey, '#');
                $value = data_get($item, $key);
                $image = data_get($item, $imageKey);
                
                return Blade::render('
                    <x-link href="{{ $url }}" class="' . $class . ' flex items-center space-x-2" :underline="false">
                        @if($image)
                            <div class="flex-shrink-0">
                                {!! $image !!}
                            </div>
                        @endif
                        <span>{{ $value }}</span>
                    </x-link>',
                    ['url' => $url, 'value' => $value, 'image' => $image]
                );
            }
        ]);
    }

    public static function badge(string $key, ?string $label = null, string $class = 'bg-primary-100 text-primary-800'): array
    {
        return self::make($key, $label, [
            'render' => function ($item) use ($key, $class) {
                $value = data_get($item, $key);
                
                return Blade::render(
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $class . '">{{ $value }}</span>',
                    ['value' => ucfirst($value)]
                );
            }
        ]);
    }

    public static function badgeMap(string $key, ?string $label = null, array $colorMap = [], string $class = ''): array
    {
        return self::make($key, $label, [
            'render' => function ($item) use ($key, $colorMap, $class) {
                $value = data_get($item, $key);
                $colorClass = $colorMap[$value] ?? 'bg-gray-100 text-gray-800';
                
                return Blade::render(
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass . ' ' . $class . '">{{ $value }}</span>',
                    ['value' => ucfirst($value)]
                );
            }
        ]);
    }

    public static function badgeLink(string $key, ?string $label = null, string $urlKey = 'url', array $colorMap = [], string $class = ''): array
    {
        return self::make($key, $label, [
            'render' => function ($item) use ($key, $urlKey, $colorMap, $class) {
                $url = data_get($item, $urlKey, '#');
                $value = data_get($item, $key);
                $colorClass = $colorMap[$value] ?? 'bg-gray-100 text-gray-800';
                
                return Blade::render(
                    '<x-link href="{{ $url }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass . ' ' . $class . ' hover:opacity-80" :underline="false">{{ $value }}</x-link>',
                    ['url' => $url, 'value' => ucfirst($value)]
                );
            }
        ]);
    }

    public static function custom(string $key, callable $render, ?string $label = null): array
    {
        return self::make($key, $label, [
            'render' => $render
        ]);
    }

    /**
     * Pre-render all data with PHP column renderers for static mode.
     */
    public static function preRenderData(array $data, array $columns): array
    {
        $renderedData = [];
        
        foreach($data as $item) {
            $renderedItem = [];
            
            foreach($columns as $column) {
                if (isset($column['render']) && is_callable($column['render'])) {
                    try {
                        $rendered = $column['render']($item, $column, data_get($item, $column['key']));
                        $renderedItem[$column['key']] = $rendered;
                    } catch (Exception $e) {
                        // Fallback to raw value if rendering fails
                        $renderedItem[$column['key']] = data_get($item, $column['key']);
                    }
                } else {
                    $renderedItem[$column['key']] = data_get($item, $column['key']);
                }
            }
            
            $renderedData[] = $renderedItem;
        }
        
        return $renderedData;
    }
}
