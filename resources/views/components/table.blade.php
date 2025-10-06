@use('App\Utilities\TableColumn')

@props([
    'data' => [],
    'columns' => [],
    'sortable' => true,
    'responsive' => true,
    'class' => '',
    'headerClass' => '',
    'headerThClass' => '',
    'bodyClass' => '',
    'rowClass' => '',
    'loading' => false,
    'emptyMessage' => __('No data available', 'fmr'),
    'sortBy' => null,
    'sortDirection' => 'asc',
    'mode' => 'livewire'
])

<div class="{{ $class }}">
    @if($loading)
        <div class="w-full">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="h-6 bg-gray-200 rounded animate-pulse w-48"></div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                @foreach($columns as $column)
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 tracking-wider">
                                        {{ $column['label'] ?? '' }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @for($i = 0; $i < 5; $i++)
                                <tr>
                                    @foreach($columns as $column)
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="h-4 bg-gray-200 rounded animate-pulse w-24"></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="{{ $responsive ? 'min-w-full overflow-x-auto' : '' }}">
            <table class="min-w-full align-middle text-sm whitespace-nowrap">
                <thead>
                    <tr class="border-b-1 border-gray-100 {{ $headerClass }}">
                        @foreach($columns as $column)
                            <th class="group px-6 py-3 font-semibold bg-white text-gray-900 {{ $headerThClass }} {{ $column['align'] ?? 'text-start' }}">
                                <div class="inline-flex items-center gap-2">
                                    <span>{{ $column['label'] ?? '' }}</span>
                                    
                                    @if($column['label'] && $sortable && ($column['sortable'] ?? true))
                                        @if($mode === 'livewire')
                                            <button
                                                type="button"
                                                @click="$dispatch('sort-table', { column: '{{ $column['key'] }}' })"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 opacity-50 group-hover:opacity-100 bg-white px-1.5 py-1 text-sm leading-5 font-semibold text-gray-800 transition hover:border-gray-300 hover:text-gray-900 hover:shadow-xs focus:ring-3 focus:ring-gray-300/25 active:border-gray-200 active:shadow-none"
                                            >
                                                @if($sortBy && $sortBy === $column['key'])
                                                    @if($sortDirection === 'asc')
                                                        <x-heroicon-o-chevron-up class="w-4 h-4" />
                                                    @else
                                                        <x-heroicon-o-chevron-down class="w-4 h-4" />
                                                    @endif
                                                @else
                                                    <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                                                @endif
                                            </button>
                                        @else
                                            @php
                                                // Three-state sorting: unsorted → asc → desc → unsorted
                                                if ($sortBy === $column['key']) {
                                                    if ($sortDirection === 'asc') {
                                                        $newSortDirection = 'desc';
                                                    } else {
                                                        // Remove sorting completely (unsorted state)
                                                        $sortUrl = request()->fullUrlWithQuery([
                                                            'sortBy' => null,
                                                            'sortDirection' => null
                                                        ]);
                                                        $newSortDirection = null;
                                                    }
                                                } else {
                                                    $newSortDirection = 'asc';
                                                }
                                                
                                                if ($newSortDirection !== null) {
                                                    $sortUrl = request()->fullUrlWithQuery([
                                                        'sortBy' => $column['key'],
                                                        'sortDirection' => $newSortDirection
                                                    ]);
                                                }
                                            @endphp
                                            <a
                                                href="{{ $sortUrl }}"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 opacity-50 group-hover:opacity-100 bg-white px-1.5 py-1 text-sm leading-5 font-semibold text-gray-800 transition hover:border-gray-300 hover:text-gray-900 hover:shadow-xs focus:ring-3 focus:ring-gray-300/25 active:border-gray-200 active:shadow-none"
                                            >
                                                @if($sortBy && $sortBy === $column['key'])
                                                    @if($sortDirection === 'asc')
                                                        <x-heroicon-o-chevron-up class="w-4 h-4" />
                                                    @else
                                                        <x-heroicon-o-chevron-down class="w-4 h-4" />
                                                    @endif
                                                @else
                                                    <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                                                @endif
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="{{ $bodyClass }}">
                    @if(!empty($data))
                        @foreach($data as $item)
                            <tr class="border-t border-gray-100 even:bg-gray-50 hover:bg-gray-50 dark:hover:bg-gray-200 {{ $rowClass }}">
                                @foreach($columns as $column)
                                    <td class="px-6 py-3 {{ $column['align'] ?? 'text-start' }}">
                                        @if(isset($column['render']) && is_callable($column['render']))
                                            {!! $column['render']($item, $column, data_get($item, $column['key'])) !!}
                                        @else
                                            {{ data_get($item, $column['key']) }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ count($columns) }}" class="px-3 py-8 text-center text-gray-500">
                                {{ $emptyMessage }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif
</div>