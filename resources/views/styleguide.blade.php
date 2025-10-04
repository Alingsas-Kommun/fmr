@extends('layouts.app')

@section('content')
    <div class="px-6 pt-8 lg:px-8">
        <div class="mx-auto max-w-7xl py-8">
            <div class="text-center mb-16">
                <h1 class="text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">Styleguide</h1>
                <p class="mt-8 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">A comprehensive guide to all available components and their variations.</p>
            </div>

            <!-- Colors Section -->
            <section class="mb-16">
                <h2 class="text-3xl font-semibold text-gray-900 mb-8">Colors</h2>
                
                <!-- Primary Colors -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Primary</h3>
                    <div class="grid grid-cols-5 md:grid-cols-11 gap-2">
                        <div class="bg-primary-50 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-950">50</div>
                        <div class="bg-primary-100 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-950">100</div>
                        <div class="bg-primary-200 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-950">200</div>
                        <div class="bg-primary-300 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-950">300</div>
                        <div class="bg-primary-400 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-950">400</div>
                        <div class="bg-primary-500 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-50">500</div>
                        <div class="bg-primary-600 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-50">600</div>
                        <div class="bg-primary-700 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-50">700</div>
                        <div class="bg-primary-800 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-50">800</div>
                        <div class="bg-primary-900 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-50">900</div>
                        <div class="bg-primary-950 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-primary-50">950</div>
                    </div>
                </div>

                <!-- Secondary Colors -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Secondary</h3>
                    <div class="grid grid-cols-5 md:grid-cols-11 gap-2">
                        <div class="bg-secondary-50 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-950">50</div>
                        <div class="bg-secondary-100 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-950">100</div>
                        <div class="bg-secondary-200 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-950">200</div>
                        <div class="bg-secondary-300 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-950">300</div>
                        <div class="bg-secondary-400 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-950">400</div>
                        <div class="bg-secondary-500 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-50">500</div>
                        <div class="bg-secondary-600 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-50">600</div>
                        <div class="bg-secondary-700 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-50">700</div>
                        <div class="bg-secondary-800 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-50">800</div>
                        <div class="bg-secondary-900 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-50">900</div>
                        <div class="bg-secondary-950 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-secondary-50">950</div>
                    </div>
                </div>

                <!-- Tertiary Colors -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Tertiary</h3>
                    <div class="grid grid-cols-5 md:grid-cols-11 gap-2">
                        <div class="bg-tertiary-50 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-950">50</div>
                        <div class="bg-tertiary-100 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-950">100</div>
                        <div class="bg-tertiary-200 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-950">200</div>
                        <div class="bg-tertiary-300 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-950">300</div>
                        <div class="bg-tertiary-400 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-950">400</div>
                        <div class="bg-tertiary-500 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-50">500</div>
                        <div class="bg-tertiary-600 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-50">600</div>
                        <div class="bg-tertiary-700 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-50">700</div>
                        <div class="bg-tertiary-800 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-50">800</div>
                        <div class="bg-tertiary-900 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-50">900</div>
                        <div class="bg-tertiary-950 aspect-square rounded-lg flex items-center justify-center text-xs font-medium text-tertiary-50">950</div>
                    </div>
                </div>
            </section>

            <!-- Buttons Section -->
            <section class="mb-16">
                <h2 class="text-3xl font-semibold text-gray-900 mb-8">Buttons</h2>
                
                <!-- Theme Colors -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Theme Colors</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Primary</h4>
                            <div class="space-y-3">
                                <x-button theme-color="primary" :link="['href' => 'https://www.google.com', 'target' => '_blank']">Primary Button</x-button>
                                <x-button theme-color="primary" theme-type="outline">Primary Outline</x-button>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Secondary</h4>
                            <div class="space-y-3">
                                <x-button theme-color="secondary">Secondary Button</x-button>
                                <x-button theme-color="secondary" theme-type="outline">Secondary Outline</x-button>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Success</h4>
                            <div class="space-y-3">
                                <x-button theme-color="success">Success Button</x-button>
                                <x-button theme-color="success" theme-type="outline">Success Outline</x-button>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Danger</h4>
                            <div class="space-y-3">
                                <x-button theme-color="danger">Danger Button</x-button>
                                <x-button theme-color="danger" theme-type="outline">Danger Outline</x-button>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Warning</h4>
                            <div class="space-y-3">
                                <x-button theme-color="warning">Warning Button</x-button>
                                <x-button theme-color="warning" theme-type="outline">Warning Outline</x-button>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Default</h4>
                            <div class="space-y-3">
                                <x-button>Default Button</x-button>
                                <x-button theme-type="outline">Default Outline</x-button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sizes -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Sizes</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-700 w-16">XS:</span>
                            <x-button size="xs" theme-color="primary">Extra Small</x-button>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-700 w-16">SM:</span>
                            <x-button size="sm" theme-color="primary">Small</x-button>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-700 w-16">MD:</span>
                            <x-button theme-color="primary">Medium (Default)</x-button>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-700 w-16">LG:</span>
                            <x-button size="lg" theme-color="primary">Large</x-button>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-700 w-16">XL:</span>
                            <x-button size="xl" theme-color="primary">Extra Large</x-button>
                        </div>
                    </div>
                </div>

                <!-- Pill Style -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Pill Style</h3>
                    <div class="space-y-3">
                        <x-button theme-color="primary" pill>Pill Button</x-button>
                        <x-button theme-color="primary" theme-type="outline" pill>Pill Outline</x-button>
                        <x-button theme-color="success" pill>Success Pill</x-button>
                        <x-button theme-color="danger" theme-type="outline" pill>Danger Pill</x-button>
                    </div>
                </div>

                <!-- With Chevron -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">With Chevron</h3>
                    <div class="space-y-3">
                        <x-button theme-color="primary" chevron>
                            Get Started
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-button>
                        <x-button theme-color="primary" theme-type="outline" chevron>
                            Learn More
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-button>
                    </div>
                </div>

                <!-- Link Buttons -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Link Buttons</h3>
                    <div class="space-y-3">
                        <x-button theme-color="primary" link="https://example.com">Link Button</x-button>
                        <x-button theme-color="primary" theme-type="outline" link="https://example.com" target="_blank">External Link</x-button>
                    </div>
                </div>

                <!-- Button Types -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Button Types</h3>
                    <div class="space-y-3">
                        <x-button type="button" theme-color="primary">Button Type</x-button>
                        <x-button type="submit" theme-color="success">Submit Type</x-button>
                        <x-button type="reset" theme-color="secondary">Reset Type</x-button>
                    </div>
                </div>
            </section>

            <!-- Tables Section -->
            <section class="mb-16">
                <h2 class="text-3xl font-semibold text-gray-900 mb-8">Tables</h2>
                
                @use('App\Utilities\TableColumn')
                    
                @set($sampleData, [
                    (object) [
                        'id' => 1,
                        'firstname' => 'Alice',
                        'lastname' => 'Johnson',
                        'email' => 'alice.johnson@example.com',
                        'status' => 'active',
                        'role' => 'admin',
                        'url' => '/users/1',
                        'party' => (object) [
                            'title' => 'Green Party',
                            'url' => '/party/green',
                            'thumbnail' => '<div class="w-4 h-4 bg-green-500 rounded-full"></div>'
                        ]
                    ],
                    (object) [
                        'id' => 2,
                        'firstname' => 'Bob',
                        'lastname' => 'Anderson',
                        'email' => 'bob.anderson@example.com',
                        'status' => 'inactive',
                        'role' => 'user',
                        'url' => '/users/2',
                        'party' => (object) [
                            'title' => 'Social Democrats',
                            'url' => '/party/social',
                            'thumbnail' => '<div class="w-4 h-4 bg-blue-500 rounded-full"></div>'
                        ]
                    ],
                    (object) [
                        'id' => 3,
                        'firstname' => 'Charlie',
                        'lastname' => 'Brown',
                        'email' => 'charlie.brown@example.com',
                        'status' => 'pending',
                        'role' => 'moderator',
                        'url' => '/users/3',
                        'party' => (object) [
                            'title' => 'Conservative Party',
                            'url' => '/party/conservative',
                            'thumbnail' => '<div class="w-4 h-4 bg-red-500 rounded-full"></div>'
                        ]
                    ],
                    (object) [
                        'id' => 4,
                        'firstname' => 'Diana',
                        'lastname' => 'Wilson',
                        'email' => 'diana.wilson@example.com',
                        'status' => 'active',
                        'role' => 'editor',
                        'url' => '/users/4',
                        'party' => (object) [
                            'title' => 'Liberal Party',
                            'url' => '/party/liberal',
                            'thumbnail' => '<div class="w-4 h-4 bg-yellow-500 rounded-full"></div>'
                        ]
                    ],
                    (object) [
                        'id' => 5,
                        'firstname' => 'Eve',
                        'lastname' => 'Davis',
                        'email' => 'eve.davis@example.com',
                        'status' => 'active',
                        'role' => 'user',
                        'url' => '/users/5',
                        'party' => (object) [
                            'title' => 'Green Party',
                            'url' => '/party/green',
                            'thumbnail' => '<div class="w-4 h-4 bg-green-500 rounded-full"></div>'
                        ]
                    ]
                ])

                @set($tableColumns, [
                    TableColumn::link('firstname', 'First Name', 'url'),
                    TableColumn::link('lastname', 'Last Name', 'url'),
                    TableColumn::text('email', 'Email', 'text-sm text-gray-600'),
                    TableColumn::badge('status', 'Status', [
                        'active' => 'bg-green-100 text-green-800',
                        'inactive' => 'bg-red-100 text-red-800',
                        'pending' => 'bg-yellow-100 text-yellow-800'
                    ]),
                    TableColumn::badge('role', 'Role', [
                        'admin' => 'bg-purple-100 text-purple-800',
                        'moderator' => 'bg-blue-100 text-blue-800',
                        'editor' => 'bg-orange-100 text-orange-800',
                        'user' => 'bg-gray-100 text-gray-800'
                    ]),
                    TableColumn::imageLink('party.title', 'Party', 'party.url', 'party.thumbnail')
                ])

                <!-- Basic Table -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Basic Table with Static Sorting</h3>
                    <p class="text-sm text-gray-600 mb-4">Click column headers to sort. Uses client-side Alpine.js sorting for instant response.</p>
                    
                    <x-table :data="$sampleData" :columns="$tableColumns" mode="static" class="w-full"/>
                </div>

                <!-- Table with Custom Styling -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Table with Custom Styling</h3>
                    <p class="text-sm text-gray-600 mb-4">Custom header and row styling with different column alignments using static mode.</p>
                    
                    @set($customColumns, [
                        TableColumn::text('id', 'ID', 'font-mono text-sm text-gray-500'),
                        TableColumn::link('firstname', 'Name', 'url', 'text-lg font-semibold'),
                        TableColumn::badge('status', 'Status', [
                            'active' => 'bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium',
                            'inactive' => 'bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium',
                            'pending' => 'bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium'
                        ]),
                        TableColumn::text('email', 'Contact', 'text-sm text-blue-600')
                    ])
                    
                    <x-table 
                        :data="$sampleData" 
                        :columns="$customColumns"
                        mode="static"
                        header-class="bg-primary-600 text-white"
                        header-th-class="text-white"
                        row-class="hover:bg-blue-50"
                        class="w-full border border-primary-700 rounded-xl overflow-hidden"
                    />
                </div>

                <!-- Non-Sortable Table -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Non-Sortable Table</h3>
                    <p class="text-sm text-gray-600 mb-4">Table without sorting functionality for display-only data using static mode.</p>
                    
                    @set($displayColumns, [
                        TableColumn::text('firstname', 'First Name'),
                        TableColumn::text('lastname', 'Last Name'),
                        TableColumn::text('email', 'Email'),
                        TableColumn::text('role', 'Role', 'capitalize')
                    ])
                    
                    <x-table 
                        :data="array_slice($sampleData, 0, 3)" 
                        :columns="$displayColumns"
                        mode="static"
                        :sortable="false"
                        class="w-full"
                    />
                </div>

                <!-- Loading State -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Loading State</h3>
                    <p class="text-sm text-gray-600 mb-4">Table showing loading skeleton while data is being fetched.</p>
                    
                    <x-table :data="[]" :columns="$tableColumns" :loading="true" class="w-full"/>
                </div>

                <!-- Empty State -->
                <div class="mb-12">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Empty State</h3>
                    <p class="text-sm text-gray-600 mb-4">Table showing empty state when no data is available.</p>
                    
                    <x-table :data="[]" :columns="$tableColumns" :empty-message="'No users found. Try adjusting your search criteria.'" class="w-full"/>
                </div>

            </section>
        </div>
    </div>
@endsection
