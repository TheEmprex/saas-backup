@extends('theme::app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($testType === 'typing')
            @include('chatter-tests.partials.typing-test', ['test' => $test])
        @elseif($testType === 'training')
            @include('chatter-tests.partials.training-test', ['test' => $test])
        @else
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6 md:p-8">
                    <h1 class="text-2xl font-bold text-gray-900">Invalid Test Type</h1>
                    <p class="mt-2 text-gray-600">The requested test type is not supported.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
