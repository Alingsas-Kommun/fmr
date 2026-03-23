<?php

namespace App\Utilities;

use function App\Core\setting;

use Exception;
use Illuminate\Support\Facades\Blade;

class TableColumn
{
    /**
     * Make a column
     * 
     * @param string $key
     * @param string|null $label
     * @param array $options
     * @return array
     */
    public static function make(string $key, ?string $label = null, array $options = []): array
    {
        return array_merge([
            'key' => $key,
            'label' => $label ?? ucfirst(str_replace(['_', '.'], ' ', $key)),
        ], $options);
    }

    /**
     * Text column renderer
     * 
     * @param string $key
     * @param string|null $label
     * @param string $class
     * @return array
     */
    public static function text(string $key, ?string $label = null, string $class = ''): array
    {
        return self::make($key, $label, [
            'render' => function ($item) use ($key, $class) {
                $value = data_get($item, $key);
                return Blade::render('<span class="' . $class . '">{{ $value }}</span>', ['value' => $value]);
            }
        ]);
    }

    /**
     * Link column renderer
     * 
     * @param string $key
     * @param string|null $label
     * @param string $urlKey
     * @param string $class
     * @return array
     */
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

    /**
     * Arrow link column renderer
     * 
     * @param string $key
     * @param string|null $label
     * @param string $urlKey
     * @param string $class
     * @return array
     */
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

    /**
     * Image link column renderer
     *
     * Optional `$options['fallback']`: `person` and custom Blade strings are shown only when
     * the image is empty and `show_person_image` is enabled. The `party` preset always shows
     * when the image is empty (party thumbnails are independent of that setting).
     *
     * @param string $key
     * @param string|null $label
     * @param string $urlKey
     * @param string $imageKey
     * @param string $class
     * @param array<string, mixed> $options
     * @return array
     */
    public static function imageLink(
        string $key,
        ?string $label = null,
        string $urlKey = 'url',
        string $imageKey = 'image',
        string $class = 'text-blue-600 hover:text-blue-800',
        array $options = []
    ): array {
        $fallbackSpec = $options['fallback'] ?? null;

        return self::make($key, $label, [
            'render' => function ($item) use ($key, $urlKey, $imageKey, $class, $fallbackSpec) {
                $url = data_get($item, $urlKey, '#');
                $value = data_get($item, $key);
                $image = data_get($item, $imageKey);

                $fallbackHtml = '';
                if (!$image && $fallbackSpec) {
                    $useFallback = ($fallbackSpec === 'party')
                        || setting('show_person_image');
                    if ($useFallback) {
                        if ($fallbackSpec === 'person' || $fallbackSpec === 'party') {
                            $fallbackHtml = self::renderImageLinkFallback($fallbackSpec);
                        } else {
                            $fallbackHtml = Blade::render($fallbackSpec);
                        }
                    }
                }

                return Blade::render(
                    '
                    <x-link href="{{ $url }}" class="' . $class . ' flex items-center space-x-2" :underline="false">
                        @if($image)
                            <div class="flex-shrink-0">
                                {!! $image !!}
                            </div>
                        @elseif($fallbackHtml)
                            {!! $fallbackHtml !!}
                        @endif
                        <span>{{ $value }}</span>
                    </x-link>',
                    ['url' => $url, 'value' => $value, 'image' => $image, 'fallbackHtml' => $fallbackHtml]
                );
            }
        ]);
    }

    /**
     * Built-in Blade markup for {@see self::imageLink()} when no image is available.
     */
    private static function renderImageLinkFallback(string $preset): string
    {
        return match ($preset) {
            'person' => Blade::render(
                '<div class="size-6 bg-gray-50 border border-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-user class="size-4 text-primary-600" />
                </div>'
            ),
            'party' => Blade::render(
                '<x-heroicon-o-user-group class="size-4 text-primary-600 flex-shrink-0" />'
            ),
            default => '',
        };
    }

    /**
     * Badge column renderer
     * 
     * @param string $key
     * @param string|null $label
     * @param string $class
     * @return array
     */
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

    /**
     * Badge map column renderer
     * 
     * @param string $key
     * @param string|null $label
     * @param array $colorMap
     * @param string $class
     * @return array
     */
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

    /**
     * Badge link column renderer
     * 
     * @param string $key
     * @param string|null $label
     * @param string $urlKey
     * @param array $colorMap
     * @param string $class
     * @return array
     */
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

    /**
     * Custom column renderer
     * 
     * @param string $key
     * @param callable $render
     * @param string|null $label
     * @return array
     */
    public static function custom(string $key, callable $render, ?string $label = null): array
    {
        return self::make($key, $label, [
            'render' => $render
        ]);
    }

    /**
     * Pre-render all data with PHP column renderers for static mode.
     * 
     * @param array $data
     * @param array $columns
     * @return array
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
