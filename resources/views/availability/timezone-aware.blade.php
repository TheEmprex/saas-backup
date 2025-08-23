@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
            <i class="fas fa-clock mr-2"></i>
            Timezone-Aware Availability
        </h2>

        <!-- Timezone Selector -->
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">
                        <i class="fas fa-globe mr-2"></i>
                        View Availability in Your Timezone
                    </h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                        Select your timezone to see when users are available in your local time
                    </p>
                </div>
                <div class="ml-4">
                    <select id="timezone-selector" class="form-select border-blue-300 dark:border-blue-600 rounded-lg">
                        <option value="">Select Timezone...</option>
                        @foreach($timezones as $tz => $label)
                            <option value="{{ $tz }}" {{ $userTimezone === $tz ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Availability Grid -->
        <div id="availability-grid" class="space-y-6">
            @if($users->count() > 0)
                @foreach($users as $user)
                    <div class="user-availability border border-gray-200 dark:border-gray-600 rounded-lg p-6" 
                         data-user-id="{{ $user->id }}">
                        
                        <!-- User Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <img src="{{ $user->avatar() }}" alt="{{ $user->name }}" 
                                     class="w-12 h-12 rounded-full mr-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $user->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $user->userType->name ?? 'User' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200">
                                    Available
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Original TZ: <span class="original-timezone" data-original-tz="{{ $user->originalTimezone ?? 'UTC' }}">
                                        {{ $user->originalTimezone ?? 'UTC' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Availability Schedule -->
                        <div class="availability-schedule grid grid-cols-1 md:grid-cols-7 gap-2">
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                <div class="day-column text-center">
                                    <div class="day-header text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ ucfirst(substr($day, 0, 3)) }}
                                    </div>
                                    <div class="day-availability" data-day="{{ $day }}">
                                        <div class="availability-slot loading text-xs text-gray-500 dark:text-gray-400 p-2 bg-gray-100 dark:bg-gray-700 rounded">
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-calendar-times text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        No Available Users Found
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        No users have set their availability yet.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timezoneSelector = document.getElementById('timezone-selector');
    const availabilityGrid = document.getElementById('availability-grid');
    
    // Load availability data when timezone changes
    timezoneSelector.addEventListener('change', function() {
        const targetTimezone = this.value;
        
        if (!targetTimezone) {
            return;
        }
        
        // Show loading state
        const userAvailabilities = document.querySelectorAll('.user-availability');
        userAvailabilities.forEach(function(userDiv) {
            const slots = userDiv.querySelectorAll('.availability-slot');
            slots.forEach(function(slot) {
                slot.innerHTML = '<div class="text-xs text-gray-500 p-2">Loading...</div>';
                slot.className = 'availability-slot loading text-xs text-gray-500 dark:text-gray-400 p-2 bg-gray-100 dark:bg-gray-700 rounded';
            });
        });
        
        // Fetch availability data for all users
        const userIds = Array.from(userAvailabilities).map(div => div.getAttribute('data-user-id'));
        
        fetch(`/api/availability/bulk?timezone=${encodeURIComponent(targetTimezone)}&user_ids=${userIds.join(',')}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateAvailabilityDisplay(data.availability, targetTimezone);
                } else {
                    console.error('Failed to load availability data');
                }
            })
            .catch(error => {
                console.error('Error fetching availability:', error);
            });
    });
    
    function updateAvailabilityDisplay(availabilityData, timezone) {
        Object.keys(availabilityData).forEach(function(userId) {
            const userDiv = document.querySelector(`[data-user-id="${userId}"]`);
            if (!userDiv) return;
            
            const userData = availabilityData[userId];
            const availability = userData.availability || [];
            
            // Group availability by day
            const availabilityByDay = {};
            availability.forEach(function(slot) {
                if (!availabilityByDay[slot.day_of_week]) {
                    availabilityByDay[slot.day_of_week] = [];
                }
                availabilityByDay[slot.day_of_week].push(slot);
            });
            
            // Update each day column
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'].forEach(function(day) {
                const dayDiv = userDiv.querySelector(`[data-day="${day}"]`);
                if (!dayDiv) return;
                
                const dayAvailability = availabilityByDay[day] || [];
                
                if (dayAvailability.length === 0) {
                    dayDiv.innerHTML = '<div class="availability-slot unavailable text-xs text-gray-500 dark:text-gray-400 p-2 bg-gray-100 dark:bg-gray-700 rounded">Not Available</div>';
                } else {
                    let slotsHtml = '';
                    dayAvailability.forEach(function(slot) {
                        const timeString = `${slot.start_time} - ${slot.end_time}`;
                        slotsHtml += `<div class="availability-slot available text-xs text-green-700 dark:text-green-300 p-2 bg-green-100 dark:bg-green-900/30 rounded mb-1">${timeString}</div>`;
                    });
                    dayDiv.innerHTML = slotsHtml;
                }
            });
        });
        
        // Update timezone display
        document.querySelectorAll('.original-timezone').forEach(function(elem) {
            const originalTz = elem.getAttribute('data-original-tz');
            elem.textContent = `${originalTz} → ${timezone}`;
        });
    }
    
    // Auto-detect user's timezone if not already selected
    if (!timezoneSelector.value) {
        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const option = timezoneSelector.querySelector(`option[value="${userTimezone}"]`);
        if (option) {
            timezoneSelector.value = userTimezone;
            timezoneSelector.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection
