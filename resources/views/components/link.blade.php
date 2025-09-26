@props([
    'href' => '#',
    'target' => null,
    'underline' => true,
])

@php
    $baseClasses = 'inline-flex items-center text-sm font-medium transition-colors duration-200';
    $colorClasses = "text-gray-700 hover:text-gray-800";
    $underlineClasses = $underline ? "!underline !decoration-secondary-300 dark:!decoration-secondary-700 underline-offset-4 hover:!underline-offset-5" : '';

    $class = trim("{$baseClasses} {$colorClasses} {$underlineClasses}");
@endphp

<a {{ $attributes->merge(['href' => $href, 'target' => $target, 'class' => $class]) }}>
    {{ $slot }}
</a>
