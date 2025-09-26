@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white dark:bg-gray-50'
])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$widthClasses = match ($width) {
    '44' => 'w-44',
    '48' => 'w-48',
    '56' => 'w-56',
    '64' => 'w-64',
    default => $width,
};
@endphp

<div 
    x-data="{ open: false }" 
    x-on:keydown.esc.prevent.stop="open = false" 
    class="relative inline-block"
>
    <!-- Dropdown Toggle Button -->
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <!-- Dropdown Content -->
    <div 
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
        @click.outside="open = false"
        class="absolute {{ $alignmentClasses }} z-50 mt-2 {{ $widthClasses }} rounded-lg shadow-lg ring-1 ring-black/5"
    >
        <div class="rounded-lg {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
