<x-layouts.app>

<div class="bg-white dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">My Contracts</h1>
                    <p class="text-gray-600 dark:text-gray-300">Manage your work contracts and track earnings</p>
                </div>
                <div>
                    <a href="{{ route('contracts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create Contract
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($contracts->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No contracts yet</h3>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Start by creating contracts with people you've messaged.</p>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($contracts as $contract)
                        <li>
                            <a href="{{ route('contracts.show', $contract) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                @if($contract->employer_id === auth()->id())
                                                    {{ substr($contract->contractor->name, 0, 1) }}
                                                @else
                                                    {{ substr($contract->employer->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    @if($contract->employer_id === auth()->id())
                                                        Contract with {{ $contract->contractor->name }}
                                                    @else
                                                        Contract with {{ $contract->employer->name }}
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $contract->contract_type }} - {{ $contract->formatted_rate }}
                                                </p>
                                                @if($contract->jobPost)
                                                    <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1">
                                                        Related to: {{ $contract->jobPost->title }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($contract->status === 'active') bg-green-100 text-green-800 
                                                @elseif($contract->status === 'completed') bg-blue-100 text-blue-800 
                                                @elseif($contract->status === 'cancelled') bg-red-100 text-red-800 
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($contract->status) }}
                                            </span>
                                            <div class="ml-4 text-right">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    ${{ number_format($contract->total_earned, 2) }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $contract->hours_worked }} hours
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ Str::limit($contract->description, 100) }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-6">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
</div>

</x-layouts.app>
