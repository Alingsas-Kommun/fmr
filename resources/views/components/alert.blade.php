@props([
    'type' => 'info',
    'message' => null,
])

@php
$config = match ($type) {
    'success' => [
        'bg' => 'bg-green-50',
        'border' => 'border-green-200',
        'text' => 'text-green-800',
        'icon' => 'heroicon-o-check-circle',
        'iconColor' => 'text-green-600'
    ],
    'warning' => [
        'bg' => 'bg-orange-50',
        'border' => 'border-orange-200',
        'text' => 'text-orange-800',
        'icon' => 'heroicon-o-exclamation-triangle',
        'iconColor' => 'text-orange-600'
    ],
    'error' => [
        'bg' => 'bg-red-50',
        'border' => 'border-red-200',
        'text' => 'text-red-800',
        'icon' => 'heroicon-o-x-circle',
        'iconColor' => 'text-red-600'
    ],
    default => [
        'bg' => 'bg-blue-50',
        'border' => 'border-blue-200',
        'text' => 'text-blue-800',
        'icon' => 'heroicon-o-information-circle',
        'iconColor' => 'text-blue-600'
    ]
};
@endphp

<div {{ $attributes->merge(['class' => "rounded-lg border {$config['bg']} {$config['border']} p-4"]) }}>
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <x-dynamic-component :component="$config['icon']" class="h-6 w-6 {{ $config['iconColor'] }}" />
        </div>

        <div class="ml-3">
            <div class="{{ $config['text'] }}">
                {!! $message ?? $slot !!}
            </div>
        </div>
    </div>
</div>
