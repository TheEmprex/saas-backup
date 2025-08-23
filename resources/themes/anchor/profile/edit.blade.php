<x-layouts.app>

<div class="bg-white dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Edit Profile</h1>
            <p class="text-gray-600 dark:text-gray-400">Update your profile information and settings.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $user->name) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $user->email) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User Type</label>
                        <div class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:text-white shadow-sm px-3 py-2 text-gray-500 dark:text-gray-400">
                            {{ $user->userType->display_name ?? 'Not Set' }}
                            <span class="text-xs block mt-1">Contact support to change your user type</span>
                        </div>
                        <!-- Hidden field to maintain current value -->
                        <input type="hidden" name="user_type_id" value="{{ $user->user_type_id }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                        <input 
                            type="tel" 
                            name="phone" 
                            value="{{ old('phone', $user->userProfile->phone ?? '') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                    <textarea 
                        name="bio" 
                        rows="4"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tell us about yourself..."
                    >{{ old('bio', $user->userProfile->bio ?? '') }}</textarea>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Picture</label>
                    
                    <!-- Current Avatar Preview -->
                    <div class="flex items-center space-x-6 mb-4">
                        <div class="shrink-0">
                            <img 
                                id="avatar-preview" 
                                class="h-24 w-24 object-cover rounded-full border-2 border-gray-300 dark:border-gray-600" 
                                src="{{ $user->getProfilePictureUrl() }}"
                                alt="Current avatar"
                            >
                        </div>
                        <div class="flex-1">
                            <input 
                                type="file" 
                                name="avatar" 
                                id="avatar-upload"
                                accept="image/*"
                                class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-200 hover:file:bg-blue-100 dark:hover:file:bg-blue-800"
                                onchange="previewAvatar(this)"
                            >
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload a profile picture (max 2MB). Image will be resized to 300x300 pixels.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Professional Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Years of Experience</label>
                        <input 
                            type="number" 
                            name="experience_years" 
                            value="{{ old('experience_years', $user->userProfile->experience_years ?? '') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            min="0"
                            max="50"
                        >
                    </div>

                    @if($user->isChatter())
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Typing Speed (WPM)</label>
                            <input 
                                type="number" 
                                name="typing_speed_wpm" 
                                value="{{ old('typing_speed_wpm', $user->userProfile->typing_speed_wpm ?? '') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="10"
                                max="200"
                            >
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hourly Rate ($)</label>
                        <input 
                            type="number" 
                            name="hourly_rate" 
                            value="{{ old('hourly_rate', $user->userProfile->hourly_rate ?? '') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            step="0.01"
                            min="0"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred Rate Type</label>
                        <select name="preferred_rate_type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Rate Type</option>
                            <option value="hourly" {{ old('preferred_rate_type', $user->userProfile->preferred_rate_type ?? '') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="fixed" {{ old('preferred_rate_type', $user->userProfile->preferred_rate_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                            <option value="commission" {{ old('preferred_rate_type', $user->userProfile->preferred_rate_type ?? '') == 'commission' ? 'selected' : '' }}>Commission</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Availability Status</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="is_available" value="1" class="mr-2" {{ old('is_available', $user->userProfile->is_available ?? true) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Available</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="is_available" value="0" class="mr-2" {{ !old('is_available', $user->userProfile->is_available ?? true) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Busy</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Response Time</label>
                    <select name="response_time" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Response Time</option>
                        <option value="within_an_hour" {{ old('response_time', $user->userProfile->response_time ?? '') == 'within_an_hour' ? 'selected' : '' }}>Within an hour</option>
                        <option value="within_a_few_hours" {{ old('response_time', $user->userProfile->response_time ?? '') == 'within_a_few_hours' ? 'selected' : '' }}>Within a few hours</option>
                        <option value="within_a_day" {{ old('response_time', $user->userProfile->response_time ?? '') == 'within_a_day' ? 'selected' : '' }}>Within a day</option>
                        <option value="within_a_few_days" {{ old('response_time', $user->userProfile->response_time ?? '') == 'within_a_few_days' ? 'selected' : '' }}>Within a few days</option>
                    </select>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Availability Description</label>
                    <textarea 
                        name="availability" 
                        rows="3"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Describe your availability (timezone, hours, etc.)"
                    >{{ old('availability', $user->userProfile->availability ?? '') }}</textarea>
                </div>
            </div>

            @if(auth()->user()->isAgency())
            <!-- Agency Information -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Agency Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monthly Revenue</label>
                        <select name="monthly_revenue" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Revenue Range</option>
                            <option value="0-5k" {{ old('monthly_revenue', $user->userProfile->monthly_revenue ?? '') == '0-5k' ? 'selected' : '' }}>$0-5k</option>
                            <option value="5-10k" {{ old('monthly_revenue', $user->userProfile->monthly_revenue ?? '') == '5-10k' ? 'selected' : '' }}>$5-10k</option>
                            <option value="10-25k" {{ old('monthly_revenue', $user->userProfile->monthly_revenue ?? '') == '10-25k' ? 'selected' : '' }}>$10-25k</option>
                            <option value="25-50k" {{ old('monthly_revenue', $user->userProfile->monthly_revenue ?? '') == '25-50k' ? 'selected' : '' }}>$25-50k</option>
                            <option value="50-100k" {{ old('monthly_revenue', $user->userProfile->monthly_revenue ?? '') == '50-100k' ? 'selected' : '' }}>$50-100k</option>
                            <option value="100-250k" {{ old('monthly_revenue', $user->userProfile->monthly_revenue ?? '') == '100-250k' ? 'selected' : '' }}>$100-250k</option>
                            <option value="250k-1m" {{ old('monthly_revenue', $user->userProfile->monthly_revenue ?? '') == '250k-1m' ? 'selected' : '' }}>$250k-1M</option>
                            <option value="1m+" {{ old('monthly_revenue', $user->userProfile->monthly_revenue ?? '') == '1m+' ? 'selected' : '' }}>$1M+</option>
                        </select>
                    </div>

                    @if(in_array(strtolower($user->userType->name ?? ''), ['chatting_agency', 'ofm_agency']))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Average LTV per Traffic ($)</label>
                        <input 
                            type="number" 
                            name="average_ltv" 
                            value="{{ old('average_ltv', $user->userProfile->average_ltv ?? '') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            step="0.01"
                            min="0"
                            placeholder="Average lifetime value"
                        >
                    </div>
                    @endif
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Traffic Types</label>
                    <div class="flex flex-wrap gap-2 mb-3" id="traffic-types-container">
                        @php
                            $trafficTypes = old('traffic_types', $user->userProfile->traffic_types ?? []);
                            if(is_string($trafficTypes)) {
                                $trafficTypes = json_decode($trafficTypes, true) ?? [];
                            }
                        @endphp
                        @foreach($trafficTypes as $trafficType)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                {{ $trafficType }}
                                <button type="button" onclick="removeTrafficType(this)" class="ml-2 text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="traffic_types[]" value="{{ $trafficType }}">
                            </span>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            id="traffic-type-input"
                            placeholder="Add a traffic type and press Enter"
                            class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onkeypress="addTrafficType(event)"
                        >
                        <button type="button" onclick="addTrafficTypeFromInput()" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                            Add
                        </button>
                    </div>
                </div>
            </div>
            @endif

            @if(!auth()->user()->isAgency() && (auth()->user()->isVa() || auth()->user()->isChatter()))
            <!-- Work Hours & Availability -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Work Hours & Availability</h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Timezone</label>
                    <select name="timezone" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select your timezone</option>
                        @php
                            $timezones = [
                                'America/New_York' => 'Eastern Time (ET)',
                                'America/Chicago' => 'Central Time (CT)',
                                'America/Denver' => 'Mountain Time (MT)',
                                'America/Los_Angeles' => 'Pacific Time (PT)',
                                'Europe/London' => 'GMT (London)',
                                'Europe/Berlin' => 'CET (Berlin)',
                                'Europe/Paris' => 'CET (Paris)',
                                'Asia/Tokyo' => 'JST (Tokyo)',
                                'Asia/Shanghai' => 'CST (Shanghai)',
                                'Asia/Manila' => 'PHT (Manila)',
                                'Australia/Sydney' => 'AEDT (Sydney)',
                                'UTC' => 'UTC'
                            ];
                        @endphp
                        @foreach($timezones as $value => $label)
                            <option value="{{ $value }}" {{ old('timezone', $user->userProfile->timezone ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Available Work Hours</label>
                    <div class="space-y-4">
                        @php
                            $workHours = old('work_hours', $user->userProfile->work_hours ?? []);
                            if(is_string($workHours)) {
                                $workHours = json_decode($workHours, true) ?? [];
                            }
                            $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            $dayLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        @endphp
                        @foreach($daysOfWeek as $index => $day)
                            <div class="flex items-center space-x-4">
                                <div class="w-24">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="work_hours[{{ $day }}][enabled]" value="1" 
                                               {{ isset($workHours[$day]['enabled']) && $workHours[$day]['enabled'] ? 'checked' : '' }}
                                               class="mr-2" onchange="toggleDayHours('{{ $day }}')"> 
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $dayLabels[$index] }}</span>
                                    </label>
                                </div>
                                <div class="flex items-center space-x-2" id="{{ $day }}-hours" style="{{ isset($workHours[$day]['enabled']) && $workHours[$day]['enabled'] ? '' : 'display: none;' }}">
                                    <input type="time" name="work_hours[{{ $day }}][start]" 
                                           value="{{ $workHours[$day]['start'] ?? '09:00' }}"
                                           class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="text-gray-500">to</span>
                                    <input type="time" name="work_hours[{{ $day }}][end]" 
                                           value="{{ $workHours[$day]['end'] ?? '17:00' }}"
                                           class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Times are in your selected timezone. Clients will see these hours converted to their local time.</p>
                </div>
            </div>
            @endif

            <!-- Feature Profile -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Feature Your Profile</h2>
                
                @php
                    $isFeaturedActive = $user->userProfile && 
                                       $user->userProfile->is_featured && 
                                       $user->userProfile->featured_until && 
                                       $user->userProfile->featured_until->isFuture();
                @endphp
                
                @if($isFeaturedActive)
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <div>
                                <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200">Your profile is currently featured!</h3>
                                <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                    @php
                                        $featuredUntil = $user->userProfile->featured_until;
                                        if ($featuredUntil && $featuredUntil->isFuture()) {
                                            $daysRemaining = max(1, ceil($featuredUntil->diffInDays(now())));
                                            $daysText = $daysRemaining . ' days remaining';
                                        } else {
                                            $daysText = 'Expired';
                                        }
                                    @endphp
                                    Featured until {{ $user->userProfile->featured_until->format('M j, Y') }} 
                                    ({{ $daysText }})
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-4">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Make your profile stand out to potential clients! Featured profiles get premium styling and better visibility.
                        </p>
                        
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-4">
                            <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">✨ Featured Profile Benefits:</h3>
                            <ul class="text-xs text-blue-600 dark:text-blue-300 space-y-1">
                                <li>• Premium visual styling with gradients and animations</li>
                                <li>• Higher visibility in search results</li>
                                <li>• Featured badge and corner ribbon</li>
                                <li>• 30 days of premium exposure</li>
                            </ul>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">$5.00</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">for 30 days</span>
                            </div>
                            <a href="{{ route('profile.feature') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                Feature My Profile
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if(!auth()->user()->isAgency())
            <!-- Skills -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Skills</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Add Skills</label>
                    <div class="flex flex-wrap gap-2 mb-3" id="skills-container">
                        @php
                            $skills = old('skills', $user->userProfile->skills ?? []);
                            if(is_string($skills)) {
                                $skills = json_decode($skills, true) ?? [];
                            }
                        @endphp
                        @foreach($skills as $skill)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $skill }}
                                <button type="button" onclick="removeSkill(this)" class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="skills[]" value="{{ $skill }}">
                            </span>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            id="skill-input"
                            placeholder="Add a skill and press Enter"
                            class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onkeypress="addSkill(event)"
                        >
                        <button type="button" onclick="addSkillFromInput()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Add
                        </button>
                    </div>
                </div>
            </div>
            @endif

            @if(!auth()->user()->isAgency())
            <!-- Services -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Services</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Services You Offer</label>
                    <div class="flex flex-wrap gap-2 mb-3" id="services-container">
                        @php
                            $services = old('services', $user->userProfile->services ?? []);
                            if(is_string($services)) {
                                $services = json_decode($services, true) ?? [];
                            }
                        @endphp
                        @foreach($services as $service)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $service }}
                                <button type="button" onclick="removeService(this)" class="ml-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="services[]" value="{{ $service }}">
                            </span>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            id="service-input"
                            placeholder="Add a service and press Enter"
                            class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onkeypress="addService(event)"
                        >
                        <button type="button" onclick="addServiceFromInput()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Add
                        </button>
                    </div>
                </div>
            </div>
            @endif

            @if(!auth()->user()->isAgency())
            <!-- Languages -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Languages</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Languages You Speak</label>
                    <div class="flex flex-wrap gap-2 mb-3" id="languages-container">
                        @php
                            $languages = old('languages', $user->userProfile->languages ?? []);
                            if(is_string($languages)) {
                                $languages = json_decode($languages, true) ?? [];
                            }
                        @endphp
                        @foreach($languages as $language)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                {{ $language }}
                                <button type="button" onclick="removeLanguage(this)" class="ml-2 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="languages[]" value="{{ $language }}">
                            </span>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            id="language-input"
                            placeholder="Add a language and press Enter"
                            class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onkeypress="addLanguage(event)"
                        >
                        <button type="button" onclick="addLanguageFromInput()" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            Add
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Portfolio -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Portfolio</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Portfolio Items</label>
                    <div class="space-y-3 mb-3" id="portfolio-container">
                        @php
                            $portfolioLinks = old('portfolio_links', $user->userProfile->portfolio_links ?? []);
                            if(is_string($portfolioLinks)) {
                                $portfolioLinks = json_decode($portfolioLinks, true) ?? [];
                            }
                        @endphp
                        @foreach($portfolioLinks as $index => $item)
                            <div class="portfolio-item p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                <div class="flex justify-between items-start mb-3">
                                    <h4 class="font-medium text-gray-900 dark:text-white">Portfolio Item {{ $index + 1 }}</h4>
                                    <button type="button" onclick="removePortfolioItem(this)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                                        <input type="text" name="portfolio_links[{{ $index }}][title]" value="{{ $item['title'] ?? '' }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL</label>
                                        <input type="url" name="portfolio_links[{{ $index }}][url]" value="{{ $item['url'] ?? '' }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                    <textarea name="portfolio_links[{{ $index }}][description]" rows="2" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $item['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addPortfolioItem()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Add Portfolio Item
                    </button>
                </div>
            </div>

            <!-- Links -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Links</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website</label>
                        <input 
                            type="url" 
                            name="website" 
                            value="{{ old('website', $user->userProfile->website ?? '') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://yourwebsite.com"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">LinkedIn URL</label>
                        <input 
                            type="url" 
                            name="linkedin_url" 
                            value="{{ old('linkedin_url', $user->userProfile->linkedin_url ?? '') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://linkedin.com/in/yourprofile"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Portfolio URL</label>
                        <input 
                            type="url" 
                            name="portfolio_url" 
                            value="{{ old('portfolio_url', $user->userProfile->portfolio_url ?? '') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://yourportfolio.com"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location</label>
                        <input 
                            type="text" 
                            name="location" 
                            value="{{ old('location', $user->userProfile->location ?? '') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="City, Country"
                        >
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('profile.show') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Skills management
function addSkill(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        addSkillFromInput();
    }
}

function addSkillFromInput() {
    const input = document.getElementById('skill-input');
    const skill = input.value.trim();
    
    if (skill) {
        addSkillTag(skill);
        input.value = '';
    }
}

function addSkillTag(skill) {
    const container = document.getElementById('skills-container');
    const span = document.createElement('span');
    span.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
    span.innerHTML = `
        ${skill}
        <button type="button" onclick="removeSkill(this)" class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <input type="hidden" name="skills[]" value="${skill}">
    `;
    container.appendChild(span);
}

function removeSkill(button) {
    button.parentElement.remove();
}

// Services management
function addService(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        addServiceFromInput();
    }
}

function addServiceFromInput() {
    const input = document.getElementById('service-input');
    const service = input.value.trim();
    
    if (service) {
        addServiceTag(service);
        input.value = '';
    }
}

function addServiceTag(service) {
    const container = document.getElementById('services-container');
    const span = document.createElement('span');
    span.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    span.innerHTML = `
        ${service}
        <button type="button" onclick="removeService(this)" class="ml-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <input type="hidden" name="services[]" value="${service}">
    `;
    container.appendChild(span);
}

function removeService(button) {
    button.parentElement.remove();
}

// Languages management
function addLanguage(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        addLanguageFromInput();
    }
}

function addLanguageFromInput() {
    const input = document.getElementById('language-input');
    const language = input.value.trim();
    
    if (language) {
        addLanguageTag(language);
        input.value = '';
    }
}

function addLanguageTag(language) {
    const container = document.getElementById('languages-container');
    const span = document.createElement('span');
    span.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
    span.innerHTML = `
        ${language}
        <button type="button" onclick="removeLanguage(this)" class="ml-2 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <input type="hidden" name="languages[]" value="${language}">
    `;
    container.appendChild(span);
}

function removeLanguage(button) {
    button.parentElement.remove();
}

// Portfolio management
function addPortfolioItem() {
    const container = document.getElementById('portfolio-container');
    const items = container.querySelectorAll('.portfolio-item');
    const index = items.length;
    
    const div = document.createElement('div');
    div.className = 'portfolio-item p-4 border border-gray-200 dark:border-gray-600 rounded-lg';
    div.innerHTML = `
        <div class="flex justify-between items-start mb-3">
            <h4 class="font-medium text-gray-900 dark:text-white">Portfolio Item ${index + 1}</h4>
            <button type="button" onclick="removePortfolioItem(this)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                <input type="text" name="portfolio_links[${index}][title]" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL</label>
                <input type="url" name="portfolio_links[${index}][url]" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
            <textarea name="portfolio_links[${index}][description]" rows="2" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
    `;
    container.appendChild(div);
}

function removePortfolioItem(button) {
    button.closest('.portfolio-item').remove();
    // Reindex remaining items
    const container = document.getElementById('portfolio-container');
    const items = container.querySelectorAll('.portfolio-item');
    items.forEach((item, index) => {
        const title = item.querySelector('h4');
        title.textContent = `Portfolio Item ${index + 1}`;
        
        // Update input names
        const inputs = item.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            if (input.name.includes('[')) {
                const baseName = input.name.replace(/\[\d+\]/, `[${index}]`);
                input.name = baseName;
            }
        });
    });
}

// Traffic types management (for agencies)
function addTrafficType(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        addTrafficTypeFromInput();
    }
}

function addTrafficTypeFromInput() {
    const input = document.getElementById('traffic-type-input');
    const trafficType = input.value.trim();
    
    if (trafficType) {
        addTrafficTypeTag(trafficType);
        input.value = '';
    }
}

function addTrafficTypeTag(trafficType) {
    const container = document.getElementById('traffic-types-container');
    const span = document.createElement('span');
    span.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
    span.innerHTML = `
        ${trafficType}
        <button type="button" onclick="removeTrafficType(this)" class="ml-2 text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <input type="hidden" name="traffic_types[]" value="${trafficType}">
    `;
    container.appendChild(span);
}

function removeTrafficType(button) {
    button.parentElement.remove();
}

// Work hours management (for VAs/chatters)
function toggleDayHours(day) {
    const checkbox = document.querySelector(`input[name="work_hours[${day}][enabled]"]`);
    const hoursDiv = document.getElementById(`${day}-hours`);
    
    if (checkbox.checked) {
        hoursDiv.style.display = 'flex';
    } else {
        hoursDiv.style.display = 'none';
    }
}

</script>

</x-layouts.app>
