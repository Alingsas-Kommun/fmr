<header class="bg-white dark:bg-gray-50">
    <div class="py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('homepage') }}" class="flex-shrink-0">
                <img src="{{ Vite::asset('resources/images/fmr-logotype.png') }}" alt="Logo" class="h-12">
            </a>

            <nav class="flex items-center space-x-8">
                <a href="{{ route('assignments.index') }}" class="text-emerald-700 hover:text-emerald-800 font-medium">
                    {!! __('Assignments', 'fmr') !!}
                </a>

                <a href="{{ route('decision-authorities.index') }}" class="text-emerald-700 hover:text-emerald-800 font-medium">
                    {!! __('Decision Authorities', 'fmr') !!}
                </a>
            </nav>
        </div>
    </div>
</header>