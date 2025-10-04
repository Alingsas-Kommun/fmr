<div x-data="{}" @sort-table.window="$dispatch('sortBy', { column: $event.detail.column })">
    @use('App\Utilities\TableColumn')

    <div class="md:bg-primary-50 rounded-xl mt-3 md:p-8">
        <form method="GET" action="{{ route('search.show') }}" class="space-y-6">
            <div class="space-y-1">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Advanced search', 'fmr') }}</h3>
                <p class="text-xs text-gray-700">{{ __('Enter a search term or select filters to find elected officials', 'fmr') }}</p>
            </div>

            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <label for="q" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Search Term', 'fmr') }}
                    </label>

                    <input 
                        type="text" 
                        name="q"
                        wire:model.live="query"
                        value="{{ $query }}"
                        class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-gray-900 placeholder:text-gray-600 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 focus:outline-hidden" 
                        placeholder="{{ __('Search elected officials...', 'fmr') }}"
                    >
                </div>

                <div class="flex items-end gap-2">
                    <button 
                        type="submit"
                        :disabled="!$wire.query"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-tertiary-500 hover:bg-tertiary-600 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 cursor-pointer"
                    >
                        <x-heroicon-o-magnifying-glass class="h-4 w-4 mr-2" />
                        {{ __('Search', 'fmr') }}
                    </button>
                    
                    @if($query || $boardId || $partyId || $roleId)
                        <button 
                            type="button"
                            wire:click="clearFilters"
                            class="inline-flex items-center px-4 py-3 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 cursor-pointer"
                        >
                            <x-heroicon-o-x-mark class="h-4 w-4 mr-1" />
                            {{ __('Clear', 'fmr') }}
                        </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="board" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Board', 'fmr') }}
                    </label>

                    <div class="relative">
                        <select 
                            name="boardId"
                            wire:model.live="boardId"
                            class="appearance-none block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white"
                        >
                            <option value="">{{ __('All Boards', 'fmr') }}</option>
                            
                            @foreach($filters['boards'] as $board)
                                <option value="{{ $board->ID }}" {{ $boardId == $board->ID ? 'selected' : '' }}>
                                    {{ $board->post_title }}
                                </option>
                            @endforeach
                        </select>
                        
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-400" />
                        </div>
                    </div>
                </div>

                <div>
                    <label for="party" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Party', 'fmr') }}
                    </label>

                    <div class="relative">
                        <select 
                            name="partyId"
                            wire:model.live="partyId"
                            class="appearance-none block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white"
                        >
                            <option value="">{{ __('All Parties', 'fmr') }}</option>
                            
                            @foreach($filters['parties'] as $party)
                                <option value="{{ $party->ID }}" {{ $partyId == $party->ID ? 'selected' : '' }}>
                                    {{ $party->post_title }}
                                </option>
                            @endforeach
                        </select>

                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-400" />
                        </div>
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('Role', 'fmr') }}
                    </label>

                    <div class="relative">
                        <select 
                            name="roleId"
                            wire:model.live="roleId"
                            class="appearance-none block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white"
                        >
                            <option value="">{{ __('All Roles', 'fmr') }}</option>
                            
                            @foreach($filters['roles'] as $role)
                                <option value="{{ $role->term_id }}" {{ $roleId == $role->term_id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-400" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $columns = [
            TableColumn::link('firstname', __('First Name', 'fmr'), 'url'),
            TableColumn::link('lastname', __('Last Name', 'fmr'), 'url'),
            TableColumn::imageLink('party.title', __('Party', 'fmr'), 'party.url', 'party.thumbnail')
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-6xl mx-auto">
            <div wire:loading class="w-full">
                <x-table :columns="$columns" :loading="true" class="w-full" />
            </div>
            
            <div wire:loading.remove>
                @if($results->isNotEmpty())
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">
                                @if($query)
                                    {{ sprintf(__('%d results found for "%s"', 'fmr'), $results->count(), $query) }}
                                @else
                                    {{ sprintf(__('%d results found', 'fmr'), $results->count()) }}
                                @endif
                            </h2>
                        </div>

                        <x-table 
                            :data="$results->toArray()" 
                            :columns="$columns"
                            :empty-message="__('No elected officials found', 'fmr')"
                            :sort-by="$sortBy"
                            :sort-direction="$sortDirection"
                            mode="dynamic"
                            class="w-full"
                        />
                    </div>
                @elseif($query || $boardId || $partyId || $roleId)
                    <x-alert type="warning">
                        {{ __('No elected officials found', 'fmr') }}
                    </x-alert>
                @endif
            </div>
        </div>
    </div>
</div>