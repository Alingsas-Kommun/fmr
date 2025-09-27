<!doctype html>
<html @php(language_attributes()) class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        @include('partials.theme-init')
        
        @action('get_header')
        @php(wp_head())

        @vite(['resources/css/app.css'])
        @livewireStyles
    </head>

    <body @php(body_class('h-full bg-white dark:bg-gray-50 text-gray-900'))>
        @php(wp_body_open())

        <div id="app">
            @include('partials.header')

            <main id="main" class="max-w-5xl px-4 mx-auto pb-8">
                @yield('content')
            </main>
        </div>

        @action('get_footer')
        @php(wp_footer())
        @livewireScripts
    </body>
</html>
