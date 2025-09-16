<x-layouts.app>
    <div class="mx-auto max-w-6xl py-8" x-data="searchPage()">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Search</h1>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow p-4 md:p-6 mb-6">
            <form method="GET" action="{{ route('search.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-3">
                    <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Query</label>
                    <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Search jobs or profiles..." class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100" x-ref="q" @input.debounce.250ms="fetchSuggestions">
                    <div x-show="suggestions.length" class="mt-2 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg shadow divide-y divide-gray-100 dark:divide-zinc-800">
                        <template x-for="item in suggestions" :key="item">
                            <button type="button" class="block w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-zinc-800" @click="$refs.q.value = item; $refs.q.form.submit();" x-text="item"></button>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Type</label>
                    <select name="type" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100">
                        <option value="all" @selected(($type ?? 'all')==='all')>All</option>
                        <option value="jobs" @selected(($type ?? '')==='jobs')>Jobs</option>
                        <option value="profiles" @selected(($type ?? '')==='profiles')>Profiles</option>
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end">
                    <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Search</button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            @if(($type ?? 'all')==='all' || ($type ?? '')==='jobs')
            <div x-transition>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Jobs</h2>
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow divide-y divide-gray-100 dark:divide-zinc-800">
                    @forelse(($jobs ?? collect()) as $job)
                        <a href="{{ route('marketplace.jobs.show', $job->id) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $job->title }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $job->market }} • {{ $job->user->name }}</div>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $job->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">No jobs found</div>
                    @endforelse
                </div>
                @if(method_exists(($jobs ?? null), 'links'))
                    <div class="mt-2">{{ $jobs->withQueryString()->links() }}</div>
                @endif
            </div>
            @endif

            @if(($type ?? 'all')==='all' || ($type ?? '')==='profiles')
            <div x-transition>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Profiles</h2>
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow divide-y divide-gray-100 dark:divide-zinc-800">
                    @forelse(($profiles ?? collect()) as $profile)
                        <a href="{{ route('marketplace.profiles.show', $profile->user->id) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $profile->user->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $profile->location }} • {{ $profile->user->userType->name ?? '' }}</div>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $profile->average_rating ?? 'N/A' }} ★</div>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">No profiles found</div>
                    @endforelse
                </div>
                @if(method_exists(($profiles ?? null), 'links'))
                    <div class="mt-2">{{ $profiles->withQueryString()->links() }}</div>
                @endif
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function searchPage() {
            return {
                suggestions: [],
                fetchSuggestions: async function () {
                    const q = this.$refs.q.value || '';
                    if (q.length < 2) { this.suggestions = []; return; }
                    try {
                        const res = await fetch(`/api/search/suggestions?q=${encodeURIComponent(q)}`);
                        const data = await res.json();
                        this.suggestions = Array.isArray(data) ? data.slice(0, 8) : [];
                    } catch (e) {
                        this.suggestions = [];
                    }
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>

