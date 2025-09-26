<header class="bg-white dark:bg-gray-50">
    <div class="py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('homepage') }}" class="flex-shrink-0">
                @if($logotype)
                    {!! $logotype !!}
                @else
                    <span class="text-2xl font-bold">{{ $siteName }}</span>
                @endif
            </a>

            <nav class="flex items-center space-x-8">
                <a href="{{ route('assignments.index') }}" class="text-primary-500 hover:text-primary-600 font-medium">
                    {!! __('Assignments', 'fmr') !!}
                </a>

                <a href="{{ route('decision-authorities.index') }}" class="text-primary-500 hover:text-primary-600 font-medium">
                    {!! __('Decision Authorities', 'fmr') !!}
                </a>
            </nav>
        </div>
    </div>
</header>