@extends('theme.default.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">⏰ Availability & Timezone Settings</h1>
            <p class="text-blue-100 mt-1">Manage your work schedule and timezone preferences</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded m-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded m-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.availability.update') }}" class="p-6">
            @csrf
            @method('PUT')

            <!-- Current Status -->
            <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Current Status</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600">Available for Work:</span>
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $user->available_for_work ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->available_for_work ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600">Timezone:</span>
                        <span class="ml-2 font-mono text-sm">{{ $user->timezone ?? 'UTC' }}</span>
                    </div>
                </div>
            </div>

            <!-- Basic Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Timezone Selection -->
                <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                        🌍 Your Timezone
                    </label>
                    <select name="timezone" id="timezone" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($timezones as $tz => $label)
                            <option value="{{ $tz }}" {{ ($user->timezone ?? 'UTC') === $tz ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">All times will be converted to this timezone</p>
                </div>

                <!-- Work Availability Toggle -->
                <div>
                    <label for="available_for_work" class="block text-sm font-medium text-gray-700 mb-2">
                        💼 Available for Work
                    </label>
                    <div class="flex items-center">
                        <input type="hidden" name="available_for_work" value="0">
                        <input type="checkbox" name="available_for_work" id="available_for_work" value="1"
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                               {{ $user->available_for_work ? 'checked' : '' }}>
                        <label for="available_for_work" class="ml-2 text-sm text-gray-600">
                            I'm currently available for new work
                        </label>
                    </div>
                </div>

                <!-- Hourly Rate -->
                <div>
                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">
                        💰 Hourly Rate
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500">$</span>
                        <input type="number" name="hourly_rate" id="hourly_rate" step="0.01" min="0" max="999.99"
                               value="{{ old('hourly_rate', $user->hourly_rate) }}"
                               class="w-full pl-8 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="25.00">
                    </div>
                </div>

                <!-- Preferred Currency -->
                <div>
                    <label for="preferred_currency" class="block text-sm font-medium text-gray-700 mb-2">
                        🏦 Preferred Currency
                    </label>
                    <select name="preferred_currency" id="preferred_currency" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @php
                            $currencies = ['USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound', 'CAD' => 'Canadian Dollar', 'AUD' => 'Australian Dollar'];
                        @endphp
                        @foreach($currencies as $code => $name)
                            <option value="{{ $code }}" {{ ($user->preferred_currency ?? 'USD') === $code ? 'selected' : '' }}>
                                {{ $code }} - {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Weekly Schedule -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📅 Weekly Schedule</h3>
                
                <!-- Template Actions -->
                <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-medium text-blue-800 mb-2">Quick Templates</h4>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="applyTemplate('weekdays')" class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm hover:bg-blue-200">
                            Mon-Fri 9-5
                        </button>
                        <button type="button" onclick="applyTemplate('fulltime')" class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm hover:bg-blue-200">
                            Mon-Fri 8-6
                        </button>
                        <button type="button" onclick="applyTemplate('parttime')" class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm hover:bg-blue-200">
                            Mon-Wed-Fri 10-4
                        </button>
                        <button type="button" onclick="applyTemplate('weekends')" class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm hover:bg-blue-200">
                            Sat-Sun 9-5
                        </button>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($days as $day => $label)
                        @php
                            $schedule = $schedules[$day] ?? null;
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4" id="day-{{ $day }}">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-800">{{ $label }}</h4>
                                <div class="flex items-center space-x-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="availability[{{ $day }}][is_available]" value="1"
                                               class="day-checkbox form-checkbox h-4 w-4 text-blue-600" data-day="{{ $day }}"
                                               {{ $schedule && $schedule->is_available ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-600">Available</span>
                                    </label>
                                    <button type="button" onclick="copyDaySchedule('{{ $day }}')" class="text-blue-600 hover:text-blue-800 text-sm">
                                        Copy to...
                                    </button>
                                </div>
                            </div>

                            <div class="day-details grid grid-cols-1 md:grid-cols-2 gap-4 {{ !$schedule || !$schedule->is_available ? 'opacity-50 pointer-events-none' : '' }}">
                                <!-- Time Range -->
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Work Hours</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="time" name="availability[{{ $day }}][start_time]"
                                               value="{{ $schedule ? $schedule->start_time : '09:00' }}"
                                               class="form-input text-sm border-gray-300 rounded">
                                        <span class="text-gray-500">to</span>
                                        <input type="time" name="availability[{{ $day }}][end_time]"
                                               value="{{ $schedule ? $schedule->end_time : '17:00' }}"
                                               class="form-input text-sm border-gray-300 rounded">
                                    </div>
                                </div>

                                <!-- Break Times -->
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Break Times (Optional)</label>
                                    <div class="space-y-2" id="breaks-{{ $day }}">
                                        @if($schedule && $schedule->break_times)
                                            @foreach($schedule->break_times as $index => $break)
                                                <div class="flex items-center space-x-2">
                                                    <input type="time" name="availability[{{ $day }}][break_times][{{ $index }}][start]"
                                                           value="{{ $break['start'] }}" class="form-input text-xs border-gray-300 rounded">
                                                    <span class="text-gray-500 text-xs">to</span>
                                                    <input type="time" name="availability[{{ $day }}][break_times][{{ $index }}][end]"
                                                           value="{{ $break['end'] }}" class="form-input text-xs border-gray-300 rounded">
                                                    <button type="button" onclick="removeBreak(this)" class="text-red-500 hover:text-red-700 text-xs">×</button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" onclick="addBreak('{{ $day }}')" class="text-blue-600 hover:text-blue-800 text-xs mt-1">
                                        + Add Break
                                    </button>
                                </div>

                                <!-- Notes -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm text-gray-600 mb-1">Notes</label>
                                    <input type="text" name="availability[{{ $day }}][notes]"
                                           value="{{ $schedule ? $schedule->notes : '' }}"
                                           placeholder="e.g., Flexible hours, meetings in morning"
                                           class="form-input text-sm border-gray-300 rounded w-full">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('profile.show') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    💾 Save Availability
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Copy Day Modal -->
<div id="copyDayModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Copy Schedule</h3>
            <p class="text-sm text-gray-600 mb-4">Copy schedule from <span id="copyFromDay" class="font-semibold"></span> to:</p>
            <div class="space-y-2" id="copyTargetDays">
                <!-- Days will be populated by JavaScript -->
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeCopyModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button onclick="confirmCopyDay()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Copy
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentCopyFromDay = '';

// Day checkbox toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const day = this.dataset.day;
            const details = document.querySelector(`#day-${day} .day-details`);
            if (this.checked) {
                details.classList.remove('opacity-50', 'pointer-events-none');
            } else {
                details.classList.add('opacity-50', 'pointer-events-none');
            }
        });
    });
});

// Template functions
function applyTemplate(templateType) {
    const templates = {
        weekdays: {
            days: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            start: '09:00',
            end: '17:00',
            breaks: []
        },
        fulltime: {
            days: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            start: '08:00',
            end: '18:00',
            breaks: [{start: '12:00', end: '13:00'}]
        },
        parttime: {
            days: ['monday', 'wednesday', 'friday'],
            start: '10:00',
            end: '16:00',
            breaks: []
        },
        weekends: {
            days: ['saturday', 'sunday'],
            start: '09:00',
            end: '17:00',
            breaks: []
        }
    };

    const template = templates[templateType];
    if (!template) return;

    // Clear all days first
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.dispatchEvent(new Event('change'));
    });

    // Apply template to specified days
    template.days.forEach(day => {
        const checkbox = document.querySelector(`input[name="availability[${day}][is_available]"]`);
        const startTime = document.querySelector(`input[name="availability[${day}][start_time]"]`);
        const endTime = document.querySelector(`input[name="availability[${day}][end_time]"]`);
        
        if (checkbox) {
            checkbox.checked = true;
            checkbox.dispatchEvent(new Event('change'));
        }
        if (startTime) startTime.value = template.start;
        if (endTime) endTime.value = template.end;

        // Clear existing breaks and add template breaks
        const breaksContainer = document.querySelector(`#breaks-${day}`);
        if (breaksContainer) {
            breaksContainer.innerHTML = '';
            template.breaks.forEach((breakTime, index) => {
                addBreak(day, breakTime);
            });
        }
    });
}

// Break management
function addBreak(day, breakTime = null) {
    const container = document.querySelector(`#breaks-${day}`);
    const breakCount = container.children.length;
    
    const breakDiv = document.createElement('div');
    breakDiv.className = 'flex items-center space-x-2';
    breakDiv.innerHTML = `
        <input type="time" name="availability[${day}][break_times][${breakCount}][start]" 
               value="${breakTime ? breakTime.start : '12:00'}" 
               class="form-input text-xs border-gray-300 rounded">
        <span class="text-gray-500 text-xs">to</span>
        <input type="time" name="availability[${day}][break_times][${breakCount}][end]" 
               value="${breakTime ? breakTime.end : '13:00'}" 
               class="form-input text-xs border-gray-300 rounded">
        <button type="button" onclick="removeBreak(this)" class="text-red-500 hover:text-red-700 text-xs">×</button>
    `;
    container.appendChild(breakDiv);
}

function removeBreak(button) {
    button.parentElement.remove();
}

// Copy day functionality
function copyDaySchedule(fromDay) {
    currentCopyFromDay = fromDay;
    document.getElementById('copyFromDay').textContent = fromDay.charAt(0).toUpperCase() + fromDay.slice(1);
    
    const targetContainer = document.getElementById('copyTargetDays');
    targetContainer.innerHTML = '';
    
    const days = {!! json_encode($days) !!};
    Object.keys(days).forEach(day => {
        if (day !== fromDay) {
            const label = document.createElement('label');
            label.className = 'inline-flex items-center';
            label.innerHTML = `
                <input type="checkbox" value="${day}" class="form-checkbox h-4 w-4 text-blue-600">
                <span class="ml-2 text-sm">${days[day]}</span>
            `;
            targetContainer.appendChild(label);
        }
    });
    
    document.getElementById('copyDayModal').classList.remove('hidden');
}

function closeCopyModal() {
    document.getElementById('copyDayModal').classList.add('hidden');
}

function confirmCopyDay() {
    const selectedDays = Array.from(document.querySelectorAll('#copyTargetDays input:checked')).map(cb => cb.value);
    
    selectedDays.forEach(toDay => {
        // Copy availability checkbox
        const fromAvailable = document.querySelector(`input[name="availability[${currentCopyFromDay}][is_available]"]`).checked;
        const toAvailable = document.querySelector(`input[name="availability[${toDay}][is_available]"]`);
        toAvailable.checked = fromAvailable;
        toAvailable.dispatchEvent(new Event('change'));
        
        // Copy times
        const fromStart = document.querySelector(`input[name="availability[${currentCopyFromDay}][start_time]"]`).value;
        const fromEnd = document.querySelector(`input[name="availability[${currentCopyFromDay}][end_time]"]`).value;
        const fromNotes = document.querySelector(`input[name="availability[${currentCopyFromDay}][notes]"]`).value;
        
        document.querySelector(`input[name="availability[${toDay}][start_time]"]`).value = fromStart;
        document.querySelector(`input[name="availability[${toDay}][end_time]"]`).value = fromEnd;
        document.querySelector(`input[name="availability[${toDay}][notes]"]`).value = fromNotes;
        
        // Copy breaks
        const fromBreaks = document.querySelectorAll(`#breaks-${currentCopyFromDay} > div`);
        const toBreaksContainer = document.querySelector(`#breaks-${toDay}`);
        toBreaksContainer.innerHTML = '';
        
        fromBreaks.forEach((breakDiv, index) => {
            const startInput = breakDiv.querySelector('input[name*="[start]"]');
            const endInput = breakDiv.querySelector('input[name*="[end]"]');
            if (startInput && endInput) {
                addBreak(toDay, {
                    start: startInput.value,
                    end: endInput.value
                });
            }
        });
    });
    
    closeCopyModal();
}
</script>

<style>
.form-input {
    @apply block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500;
}
.form-checkbox {
    @apply rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50;
}
</style>
@endsection
