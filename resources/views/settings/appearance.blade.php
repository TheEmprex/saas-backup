<x-layouts.app>
    <div class="max-w-3xl mx-auto py-8" x-data="appearancePage()">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Appearance</h1>

        @if(session('success'))
            <div class="mb-4 p-3 rounded bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('settings.appearance.update') }}" x-transition>
            @csrf
            @method('PUT')
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow divide-y divide-gray-100 dark:divide-zinc-800">
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php $current = $appearance['theme'] ?? 'system'; @endphp
                        @foreach([['light','☀️','Light'],['dark','🌙','Dark'],['system','💻','System']] as [$value,$icon,$label])
                            <label class="block border rounded-xl p-4 cursor-pointer hover:border-indigo-400 transition dark:border-zinc-700" :class="theme==='{{ $value }}' ? 'border-indigo-500' : ''">
                                <div class="flex items-center gap-3">
                                    <div class="text-xl">{{ $icon }}</div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $label }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">@if($value==='system') Match device preference @elseif($value==='dark') Easy on eyes @else Clean and bright @endif</div>
                                    </div>
                                    <input type="radio" name="theme" value="{{ $value }}" class="ml-auto" @checked($current === $value) @change="setTheme('{{ $value }}')">
                                </div>
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

        <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
            Tip: Theme is also applied instantly on this device using your saved preference.
        </div>
    </div>

    @push('scripts')
    <script>
        function appearancePage() {
            return {
                theme: @json($appearance['theme'] ?? 'system'),
                setTheme(val) {
                    this.theme = val
                    try { localStorage.setItem('onlyverified-theme', val) } catch (e) {}
                    const root = document.documentElement
                    root.classList.remove('light', 'dark')
                    if (val !== 'system') root.classList.add(val)
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>

