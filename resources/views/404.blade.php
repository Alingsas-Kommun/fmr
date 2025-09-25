@extends('layouts.app')

@section('content')
    @noposts
        <main class="grid min-h-full place-items-center px-6 py-24 sm:py-32 lg:px-8">
            <div class="text-center">
                <p class="font-semibold text-primary-600 text-2xl">{!! __('404', 'fmr') !!}</p>
                <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">{!! __('Page not found', 'fmr') !!}</h1>
                <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">{!! __('Sorry, we couldn’t find the page you’re looking for.', 'fmr') !!}</p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="{!! route('homepage') !!}" class="rounded-md bg-primary-500 px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-primary-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500">
                        {!! __('Go back home', 'fmr') !!}
                    </a>
                    {{-- <x-button theme-color="primary" :link="['href' => route('homepage')]">
                        {!! __('Go back home', 'fmr') !!}
                    </x-button> --}}
                </div>
            </div>
        </main>
    @endnoposts
@endsection
