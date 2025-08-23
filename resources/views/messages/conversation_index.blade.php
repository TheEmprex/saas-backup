@extends('theme::app')

@section('title', 'Conversations')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="rounded-lg h-96">
            <h1 class="text-2xl font-bold mb-6">Conversations</h1>
            <div class="grid gap-6">
                @foreach ($conversations as $conversation)
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">{{ $conversation['other_user']['name'] }}</h2>
                                <p class="text-sm text-gray-600">Last message at {{ $conversation['updated_at']->format('M j, g:i A') }}</p>
                            </div>
                            <a href="{{ route('messages.show', $conversation['other_user_id']) }}" class="text-blue-600 hover:text-blue-800">View</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

