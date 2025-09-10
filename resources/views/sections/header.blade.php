<header class="bg-white">
    <div class="mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('homepage') }}" class="flex-shrink-0">
                <img src="{{ Vite::asset('resources/images/fmr-logotype.png') }}" alt="Logo" class="h-12">
            </a>

            <nav class="flex items-center space-x-8">
                <a href="{{ get_post_type_archive_link('party') }}" class="text-green-700 hover:text-green-800 font-medium">
                    {!! __('Parties', 'fmr') !!}
                </a>

                <a href="{{ route('assignments.index') }}" class="text-green-700 hover:text-green-800 font-medium">
                    {!! __('Assignments', 'fmr') !!}
                </a>

                <a href="{!! get_post_type_archive_link('person') !!}" class="text-green-700 hover:text-green-800 font-medium">
                    {!! __('Persons', 'fmr') !!}
                </a>

                <a href="{{ get_post_type_archive_link('board') }}" class="text-green-700 hover:text-green-800 font-medium">
                    {!! __('Boards', 'fmr') !!}
                </a>

                <a href="{{ route('decision-authorities.index') }}" class="text-green-700 hover:text-green-800 font-medium">
                    {!! __('Decision Authorities', 'fmr') !!}
                </a>
            </nav>
        </div>
    </div>
</header>

<div class="bg-[#cadfde] rounded-xl">
    <div class="mx-auto max-w-3xl py-15 sm:py-20 lg:py-25">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-teal-700 sm:text-5xl text-balance">
                {!! __('Find your politician in Alingsås', 'fmr') !!}
            </h1>
            <p class="mt-6 text-lg leading-8 text-gray-800">
                {!! __('Search through assignments, parties and politicians in the municipality of Alingsås.', 'fmr') !!}
            </p>
            
            <div class="mt-10">
                <form action="#" method="GET" class="max-w-lg mx-auto">
                    <div class="flex gap-x-4">
                        <label for="search" class="sr-only">{!! __('Search', 'fmr') !!}</label>
                        <div class="flex-auto">
                            <input type="text" 
                                    name="search" 
                                    id="search" 
                                    class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" 
                                    placeholder="{!! __('Enter name, party or role...', 'fmr') !!}">
                        </div>
                        <button type="submit" 
                                class="flex-none rounded-lg bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                            {!! __('Search', 'fmr') !!}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>