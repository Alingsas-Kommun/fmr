@props([
    'href' => '#',
    'icon' => null,
    'external' => false
])

<a 
    href="{{ $href }}" 
    {{ $external ? 'target="_blank" rel="noopener"' : '' }}
    {{ $attributes->merge(['class' => 'group flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:rounded-sm transition-colors duration-200']) }}
    role="menuitem"
>
    @if($icon)
        <span class="flex-shrink-0 opacity-60 group-hover:opacity-100">
            {{ $icon }}
        </span>
    @endif
    
    <span class="flex-grow">
        {{ $slot }}
    </span>
</a>
