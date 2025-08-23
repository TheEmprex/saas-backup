@extends('theme.default.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">🌍 Find Users by Availability</h1>
            <p class="text-indigo-100 mt-1">Search users based on their timezone and availability</p>
        </div>

        <!-- Filters -->
        <div class="p-6 bg-gray-50 border-b">
            <form id="availabilityFilter" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Timezone Filter -->
                <div>
                    <label for="viewTimezone" class="block text-sm font-medium text-gray-700 mb-2">
                        View in Timezone
                    </label>
                    <select id="viewTimezone" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @php
                            $commonTimezones = [
                                'UTC' => 'UTC',
                                'America/New_York' => 'Eastern Time',
                                'America/Chicago' => 'Central Time',
                                'America/Denver' => 'Mountain Time',
                                'America/Los_Angeles' => 'Pacific Time',
                                'Europe/London' => 'GMT/BST',
                                'Europe/Paris' => 'CET/CEST',
                                'Asia/Tokyo' => 'JST',
                                'Australia/Sydney' => 'AEST/AEDT',
                            ];
                        @endphp
                        @foreach($commonTimezones as $tz => $label)
                            <option value="{{ $tz }}" {{ $tz === 'UTC' ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Day Filter -->
                <div>
                    <label for="dayFilter" class="block text-sm font-medium text-gray-700 mb-2">
                        Day of Week
                    </label>
                    <select id="dayFilter" class="w-full p-3 border border-gray-300 rounded-lg">
                        <option value="">All Days</option>
                        <option value="monday">Monday</option>
                        <option value="tuesday">Tuesday</option>
                        <option value="wednesday">Wednesday</option>
                        <option value="thursday">Thursday</option>
                        <option value="friday">Friday</option>
                        <option value="saturday">Saturday</option>
                        <option value="sunday">Sunday</option>
                    </select>
                </div>

                <!-- Time Range Filter -->
                <div>
                    <label for="timeFilter" class="block text-sm font-medium text-gray-700 mb-2">
                        Available Between
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="time" id="startTime" value="09:00" class="flex-1 p-2 border border-gray-300 rounded">
                        <span class="text-gray-500">-</span>
                        <input type="time" id="endTime" value="17:00" class="flex-1 p-2 border border-gray-300 rounded">
                    </div>
                </div>

                <!-- Search Button -->
                <div class="flex items-end">
                    <button type="button" onclick="searchUsers()" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        🔍 Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="hidden p-8 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Searching users...</p>
        </div>

        <!-- Results -->
        <div id="resultsContainer" class="p-6">
            <div class="text-center text-gray-500 py-8">
                <p>Use the filters above to search for available users</p>
            </div>
        </div>
    </div>
</div>

<!-- User Card Template (Hidden) -->
<div id="userCardTemplate" class="hidden">
    <div class="user-card bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center">
                <img class="user-avatar w-12 h-12 rounded-full object-cover" src="" alt="">
                <div class="ml-4">
                    <h3 class="user-name text-lg font-semibold text-gray-900"></h3>
                    <p class="user-type text-sm text-gray-600"></p>
                    <div class="flex items-center mt-1">
                        <span class="user-timezone text-xs text-gray-500 font-mono"></span>
                        <span class="ml-2 user-status px-2 py-1 text-xs rounded-full"></span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="user-rate text-lg font-bold text-green-600"></div>
                <div class="user-rating flex items-center justify-end mt-1">
                    <!-- Stars will be added here -->
                </div>
            </div>
        </div>

        <div class="availability-schedule mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Availability (in your timezone)</h4>
            <div class="availability-days grid grid-cols-7 gap-1 text-xs">
                <!-- Days will be added here -->
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex space-x-2">
                <button onclick="viewProfile(this)" class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm hover:bg-blue-200">
                    View Profile
                </button>
                <button onclick="sendMessage(this)" class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm hover:bg-green-200">
                    Message
                </button>
            </div>
            <div class="text-xs text-gray-500">
                Last active: <span class="user-last-active"></span>
            </div>
        </div>
    </div>
</div>

<script>
let currentResults = [];

function searchUsers() {
    const viewTimezone = document.getElementById('viewTimezone').value;
    const dayFilter = document.getElementById('dayFilter').value;
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;

    // Show loading state
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('resultsContainer').innerHTML = '';

    // Simulate API call (replace with actual endpoint)
    const params = new URLSearchParams({
        timezone: viewTimezone,
        day: dayFilter,
        start_time: startTime,
        end_time: endTime
    });

    fetch(`/api/users/search-availability?${params}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingState').classList.add('hidden');
            displayResults(data.users || []);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('resultsContainer').innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <p>Error loading users. Please try again.</p>
                </div>
            `;
        });
}

function displayResults(users) {
    const container = document.getElementById('resultsContainer');
    
    if (users.length === 0) {
        container.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                <p>No users found matching your criteria.</p>
                <p class="text-sm mt-2">Try adjusting your filters and search again.</p>
            </div>
        `;
        return;
    }

    const resultsHtml = `
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Found ${users.length} available user${users.length !== 1 ? 's' : ''}</h3>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            ${users.map(user => createUserCard(user)).join('')}
        </div>
    `;
    
    container.innerHTML = resultsHtml;
}

function createUserCard(user) {
    const ratingStars = generateStars(user.rating || 0);
    const statusClass = user.available_for_work ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
    const statusText = user.available_for_work ? 'Available' : 'Busy';
    
    return `
        <div class="user-card bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-user-id="${user.user_id}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center">
                    <img class="w-12 h-12 rounded-full object-cover" src="${user.avatar || '/images/default-avatar.png'}" alt="${user.user_name}">
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">${user.user_name}</h3>
                        <p class="text-sm text-gray-600">${user.user_type || 'User'}</p>
                        <div class="flex items-center mt-1">
                            <span class="text-xs text-gray-500 font-mono">${user.user_timezone}</span>
                            <span class="ml-2 px-2 py-1 text-xs rounded-full ${statusClass}">${statusText}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-green-600">
                        ${user.hourly_rate ? '$' + user.hourly_rate + '/' + (user.preferred_currency || 'USD') : 'Rate TBD'}
                    </div>
                    <div class="flex items-center justify-end mt-1">
                        ${ratingStars}
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Availability (in your timezone)</h4>
                <div class="grid grid-cols-7 gap-1 text-xs">
                    ${generateAvailabilityDays(user.availability)}
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex space-x-2">
                    <button onclick="viewProfile(${user.user_id})" class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm hover:bg-blue-200">
                        View Profile
                    </button>
                    <button onclick="sendMessage(${user.user_id})" class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm hover:bg-green-200">
                        Message
                    </button>
                </div>
                <div class="text-xs text-gray-500">
                    Last active: ${user.last_active || 'Unknown'}
                </div>
            </div>
        </div>
    `;
}

function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let stars = '';
    
    for (let i = 0; i < 5; i++) {
        if (i < fullStars) {
            stars += '<span class="text-yellow-400">★</span>';
        } else if (i === fullStars && hasHalfStar) {
            stars += '<span class="text-yellow-400">☆</span>';
        } else {
            stars += '<span class="text-gray-300">★</span>';
        }
    }
    
    return stars;
}

function generateAvailabilityDays(availability) {
    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const dayMap = {
        'monday': 0, 'tuesday': 1, 'wednesday': 2, 'thursday': 3,
        'friday': 4, 'saturday': 5, 'sunday': 6
    };
    
    return days.map((day, index) => {
        const dayName = Object.keys(dayMap).find(key => dayMap[key] === index);
        const dayAvailability = availability.find(a => a.day_of_week === dayName);
        
        if (dayAvailability && dayAvailability.is_available) {
            return `
                <div class="bg-green-100 text-green-800 p-2 rounded text-center border">
                    <div class="font-semibold">${day}</div>
                    <div class="text-xs mt-1">${dayAvailability.start_time_converted || dayAvailability.start_time}</div>
                    <div class="text-xs">-</div>
                    <div class="text-xs">${dayAvailability.end_time_converted || dayAvailability.end_time}</div>
                </div>
            `;
        } else {
            return `
                <div class="bg-gray-100 text-gray-500 p-2 rounded text-center border">
                    <div class="font-semibold">${day}</div>
                    <div class="text-xs mt-1">Not</div>
                    <div class="text-xs">Available</div>
                </div>
            `;
        }
    }).join('');
}

function viewProfile(userId) {
    window.open(`/marketplace/profiles/${userId}`, '_blank');
}

function sendMessage(userId) {
    window.location.href = `/messages/create/${userId}`;
}

// Auto-detect user's timezone
document.addEventListener('DOMContentLoaded', function() {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const timezoneSelect = document.getElementById('viewTimezone');
    
    // Try to set user's detected timezone
    for (let option of timezoneSelect.options) {
        if (option.value === userTimezone) {
            option.selected = true;
            break;
        }
    }
});
</script>

<style>
.user-card {
    transition: all 0.2s ease-in-out;
}

.user-card:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .grid-cols-7 {
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.25rem;
    }
    
    .grid-cols-7 > div {
        padding: 0.25rem;
        font-size: 0.625rem;
    }
}
</style>
@endsection
