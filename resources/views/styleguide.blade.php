@extends('layouts.app')

@section('content')
    <div class="px-6 pt-8 lg:px-8">
        <div class="mx-auto max-w-7xl py-8">
            <div class="text-center mb-16">
                <h1 class="text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">Styleguide</h1>
                <p class="mt-8 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">A comprehensive guide to all available components and their variations.</p>
            </div>

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
        </div>
    </div>
@endsection
