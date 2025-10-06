@props(['breadcrumbs' => []])

@if(!empty($breadcrumbs))
    <nav class="flex items-center space-x-1 text-sm text-gray-600 py-2 overflow-x-auto" aria-label="Breadcrumb">
        <ol class="flex items-center">
            @foreach($breadcrumbs as $index => $breadcrumb)
                <li class="flex items-center">
                    @set($hasValidUrl, !empty($breadcrumb['url']) && $breadcrumb['url'] !== '#')
                    @set($isClickable, $hasValidUrl && !$breadcrumb['current'])
                    
                    @if($isClickable)
                        <a class="flex items-center space-x-1.5 px-2 py-1 rounded-md hover:bg-gray-100 transition-colors duration-150 text-gray-600 hover:text-gray-900" href="{{ $breadcrumb['url'] }}">
                            @if($breadcrumb['icon'])
                                <x-dynamic-component :component="$breadcrumb['icon']" class="w-3.5 h-3.5" />
                            @endif

                            <span class="truncate max-w-[200px]">{{ $breadcrumb['label'] }}</span>
                        </a>
                    @else
                        <span class="flex items-center space-x-1.5 px-2 py-1 {{ $breadcrumb['current'] ? 'text-gray-900 font-medium' : 'text-gray-600' }}" {{ $breadcrumb['current'] ? 'aria-current="page"' : '' }}>
                            @if($breadcrumb['icon'])
                                <x-dynamic-component :component="$breadcrumb['icon']" class="w-3.5 h-3.5" />
                            @endif
                            
                            <span class="truncate max-w-[200px]">{{ $breadcrumb['label'] }}</span>
                        </span>
                    @endif
                    
                    @if(!$loop->last)
                        <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-400 mx-1" />
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif