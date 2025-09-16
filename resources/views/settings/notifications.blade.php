<x-layouts.app>
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Notification Settings</h1>

        @if(session('success'))
            <div class="mb-4 p-3 rounded bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('settings.notifications.update') }}" x-data="{enabled: {{ $prefs['enabled'] ? 'true' : 'false' }}, sounds: {{ $prefs['sounds'] ? 'true' : 'false' }}, start: '{{ $prefs['quiet_hours']['start'] ?? '' }}', end: '{{ $prefs['quiet_hours']['end'] ?? '' }}'}" x-transition>
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow divide-y divide-gray-100 dark:divide-zinc-800">
                <div class="p-5">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">Enable notifications</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Receive push alerts on new messages and activity</div>
                        </div>
                        <input type="hidden" name="enabled" :value="enabled ? 1 : 0">
                        <button type="button" @click="enabled=!enabled" :class="enabled ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-zinc-700'" class="relative inline-flex h-6 w-11 items-center rounded-full transition">
                            <span :class="enabled ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition"></span>
                        </button>
                    </label>
                </div>

                <div class="p-5">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">Sounds</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Play a sound for incoming messages</div>
                        </div>
                        <input type="hidden" name="sounds" :value="sounds ? 1 : 0">
                        <button type="button" @click="sounds=!sounds" :class="sounds ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-zinc-700'" class="relative inline-flex h-6 w-11 items-center rounded-full transition">
                            <span :class="sounds ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition"></span>
                        </button>
                    </label>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Quiet hours start</label>
                        <input type="time" name="quiet_hours_start" x-model="start" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Quiet hours end</label>
                        <input type="time" name="quiet_hours_end" x-model="end" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100">
                    </div>
                </div>

                <div class="p-5">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Topics</div>
                    <div class="flex flex-wrap gap-2">
                        @php $topics = $prefs['topics'] ?? []; @endphp
                        @foreach(['messages'=>'Messages','jobs'=>'Jobs','system'=>'System'] as $key=>$label)
                            <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border dark:border-zinc-700 cursor-pointer">
                                <input type="checkbox" name="topics[]" value="{{ $key }}" class="rounded border-gray-300 dark:border-zinc-600" @checked(in_array($key, $topics))>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="p-5 flex items-center justify-end gap-3">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-gray-200">Cancel</a>
                    <button class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white">Save</button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>

