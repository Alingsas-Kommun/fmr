@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('persons.index') }}" class="text-green-700 hover:text-green-800">
                &larr; Back to Persons
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <h1 class="text-3xl font-bold mb-6">{{ $person->post_title }}</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        @php
                            $first_name = $person->getMeta('person_firstname');
                            $last_name = $person->getMeta('person_lastname');
                            $birth_date = $person->getMeta('person_birth_date');
                            $ssn = $person->getMeta('person_ssn');
                            $email = $person->getMeta('person_home_email');
                            $phone = $person->getMeta('person_home_phone');
                            $mobile = $person->getMeta('person_home_mobile');
                        @endphp

                        @if($thumbnail)
                            <div class="mb-6">
                                {!! $thumbnail !!}
                            </div>
                        @endif

                        @if($first_name && $last_name)
                            <h2 class="text-lg font-semibold mb-2">{{ $first_name }} {{ $last_name }}</h2>
                        @endif

                        <div class="space-y-4">
                            @if($birth_date)
                                <p class="text-gray-700">
                                    <span class="font-medium">Birth date:</span>
                                    {{ $birth_date }}
                                </p>
                            @endif

                            @if($ssn)
                                <p class="text-gray-700">
                                    <span class="font-medium">Social security number:</span>
                                    {{ $ssn }}
                                </p>
                            @endif

                            @if($email)
                                <p class="text-gray-700">
                                    <span class="font-medium">Email:</span>
                                    <a href="mailto:{{ $email }}" class="text-primary hover:text-primary-600">
                                        {{ $email }}
                                    </a>
                                </p>
                            @endif

                            @if($phone)
                                <p class="text-gray-700">
                                    <span class="font-medium">Phone:</span>
                                    <a href="tel:{{ $phone }}" class="text-primary hover:text-primary-600">
                                        {{ $phone }}
                                    </a>
                                </p>
                            @endif

                            @if($mobile)
                                <p class="text-gray-700">
                                    <span class="font-medium">Mobile:</span>
                                    <a href="tel:{{ $mobile }}" class="text-primary hover:text-primary-600">
                                        {{ $mobile }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div>
                        @if($person->post_content)
                            <div class="prose max-w-none">
                                {!! wp_kses_post($person->post_content) !!}
                            </div>
                        @endif
                    </div>
                </div>

                @if($assignments->isNotEmpty())
                    <div class="mt-8">
                        <h2 class="text-2xl font-semibold mb-4">Current Assignments</h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <ul class="space-y-4">
                                @foreach($assignments as $assignment)
                                    <li class="flex items-center justify-between">
                                        <div>
                                            <p>{{ $assignment->id }}</p>
                                            <p class="font-medium">{{ $assignment->board->post_title }}</p>
                                            <p class="text-gray-600">{{ $assignment->role }}</p>
                                        </div>
                                        <p class="text-gray-600">
                                            {{ $assignment->period_start->format('Y-m-d') }} - 
                                            {{ $assignment->period_end->format('Y-m-d') }}
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection