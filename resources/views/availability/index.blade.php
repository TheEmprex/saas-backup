@extends('theme::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">My Availability</h1>
            <p class="text-gray-600">Manage your work schedule and timezone preferences</p>
        </div>

        <!-- Quick Templates -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Templates</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach(['weekdays_9_5' => 'Weekdays 9-5', 'weekdays_flexible' => 'Weekdays 8-6', 'weekends' => 'Weekends Only', 'full_time' => 'Full Time'] as $template => $label)
                <form action="{{ route('availability.template') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="template" value="{{ $template }}">
                    <input type="hidden" name="timezone" value="UTC">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg transition-colors">
                        {{ $label }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>

        <!-- Current Availability -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Weekly Schedule</h2>
                <a href="{{ route('availability.edit') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Edit Schedule
                </a>
            </div>

            <div class="space-y-4">
                @foreach($daysOfWeek as $day => $label)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-20 text-sm font-medium text-gray-700 capitalize">
                            {{ $label }}
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @if(isset($availability[$day]) && $availability[$day]->isNotEmpty())
                            @foreach($availability[$day] as $slot)
                                @if($slot->is_available)
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Available
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            {{ date('g:i A', strtotime($slot->start_time)) }} - {{ date('g:i A', strtotime($slot->end_time)) }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $slot->timezone }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Not Available
                                    </span>
                                @endif
                            @endforeach
                        @else
                            <span class="text-sm text-gray-500">No schedule set</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Timezone Info -->
        <div class="bg-blue-50 rounded-lg p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Timezone Information
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Your availability will be displayed to agencies in their local timezone. Make sure to set your preferred timezone when editing your schedule.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-detect user's timezone for templates
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.querySelectorAll('input[name="timezone"]').forEach(input => {
        input.value = timezone;
    });
});
</script>
@endpush
@endsection
