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
    'mode' => 'static'
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
        @if($mode === 'static')
            @set($renderedData, TableColumn::preRenderData($data, $columns))
            
            <div 
                x-data="{
                    data: @js($data),
                    renderedData: @js($renderedData),
                    columns: @js($columns),
                    sortBy: @js($sortBy),
                    sortDirection: @js($sortDirection),
                    sortable: @js($sortable),
                    sortedData: [],
                    init() {
                        this.sortedData = this.renderedData;
                        if (this.sortBy) {
                            this.sortByColumn(this.sortBy);
                        }
                    },
                    sortByColumn(column) {
                        if (!this.sortable) return;
                        
                        if (this.sortBy === column) {
                            if (this.sortDirection === 'asc') {
                                this.sortDirection = 'desc';
                            } else if (this.sortDirection === 'desc') {
                                // Reset to unsorted state
                                this.sortBy = null;
                                this.sortDirection = 'asc';
                            }
                        } else {
                            this.sortDirection = 'asc';
                            this.sortBy = column;
                        }
                        
                        if (this.sortBy) {
                            // Sort using original data, but return rendered data
                            const sortedIndices = [...Array(this.data.length).keys()].sort((a, b) => {
                                const aVal = this.getNestedValue(this.data[a], column);
                                const bVal = this.getNestedValue(this.data[b], column);
                                
                                if (aVal < bVal) {
                                    return this.sortDirection === 'asc' ? -1 : 1;
                                }
                                if (aVal > bVal) {
                                    return this.sortDirection === 'asc' ? 1 : -1;
                                }
                                return 0;
                            });
                            
                            this.sortedData = sortedIndices.map(index => this.renderedData[index]);
                        } else {
                            // Reset to original order when unsorted
                            this.sortedData = [...this.renderedData];
                        }
                    },
                    getNestedValue(obj, path) {
                        return path.split('.').reduce((o, p) => o && o[p], obj);
                    },
                    renderColumn(item, column) {
                        // The item is from renderedData, so it already contains the pre-rendered HTML
                        return item[column.key] || '';
                    }
                }"
                class="{{ $responsive ? 'min-w-full overflow-x-auto rounded-lg' : '' }}"
            >
                <table class="min-w-full align-middle text-sm whitespace-nowrap">
                    <thead>
                        <tr class="border-b-1 border-gray-100 {{ $headerClass }}">
                            <template x-for="column in columns" :key="column.key">
                                <th class="group px-6 py-3 font-semibold bg-white text-gray-900 {{ $headerThClass }}" :class="column.align || 'text-start'">
                                    <div class="inline-flex items-center gap-2">
                                        <span x-text="column.label || ''"></span>
                                        
                                        <template x-if="column.label && sortable && (column.sortable !== false)">
                                            <button
                                                type="button"
                                                @click="sortByColumn(column.key)"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 opacity-50 group-hover:opacity-100 bg-white px-1.5 py-1 text-sm leading-5 font-semibold text-gray-800 transition hover:border-gray-300 hover:text-gray-900 hover:shadow-xs focus:ring-3 focus:ring-gray-300/25 active:border-gray-200 active:shadow-none"
                                            >
                                                <template x-if="sortBy === column.key && sortDirection === 'asc'">
                                                    <x-heroicon-o-chevron-up class="w-4 h-4" />
                                                </template>
                                                <template x-if="sortBy === column.key && sortDirection === 'desc'">
                                                    <x-heroicon-o-chevron-down class="w-4 h-4" />
                                                </template>
                                                <template x-if="sortBy !== column.key">
                                                    <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                                                </template>
                                            </button>
                                        </template>
                                    </div>
                                </th>
                            </template>
                        </tr>
                    </thead>

                    <tbody class="{{ $bodyClass }}">
                        <template x-for="(item, index) in sortedData" :key="item.id || index">
                            <tr class="border-t border-gray-100 even:bg-gray-50 hover:bg-gray-50 dark:hover:bg-gray-200 {{ $rowClass }}">
                                <template x-for="column in columns" :key="column.key">
                                    <td class="px-6 py-3" :class="column.align || 'text-start'">
                                        <div x-html="renderColumn(item, column)" class="flex"></div>
                                    </td>
                                </template>
                            </tr>
                        </template>
                        
                        <template x-if="sortedData.length === 0">
                            <tr>
                                <td :colspan="columns.length" class="px-3 py-8 text-center text-gray-500">
                                    {{ $emptyMessage }}
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        @else
            <div class="{{ $responsive ? 'min-w-full overflow-x-auto' : '' }}">
                <table class="min-w-full align-middle text-sm whitespace-nowrap">
                    <thead>
                        <tr class="border-b-1 border-gray-100 {{ $headerClass }}">
                            @foreach($columns as $column)
                                <th class="group px-6 py-3 font-semibold bg-white text-gray-900 {{ $column['align'] ?? 'text-start' }}">
                                    <div class="inline-flex items-center gap-2">
                                        <span>{{ $column['label'] ?? '' }}</span>
                                        
                                        @if($column['label'] && $sortable && ($column['sortable'] ?? true))
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
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="{{ $bodyClass }}">
                        @if(!empty($data))
                            @foreach($data as $item)
                                <tr class="border-t border-gray-100 even:bg-gray-50 hover:bg-gray-50 {{ $rowClass }}">
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
    @endif
</div>