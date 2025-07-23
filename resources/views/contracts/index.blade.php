<x-layouts.app>

<div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        My 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Contracts</span>
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">Manage your work contracts and track earnings</p>
                </div>
                <div>
                    <a href="{{ route('contracts.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg transition-all duration-200 transform hover:scale-105">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create Contract
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        @if(!$contracts->isEmpty())
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Contracts</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $contracts->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Earnings</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($contracts->sum('total_earned'), 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hours Worked</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $contracts->sum('hours_worked') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Contracts</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $contracts->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

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
            <!-- Contract Cards Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($contracts as $contract)
                    <div class="group h-full">
                        <a href="{{ route('contracts.show', $contract) }}" class="block h-full">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg border-2 h-full flex flex-col
                                @if($contract->status === 'active') border-green-200 dark:border-green-700 bg-gradient-to-r from-green-50/30 to-white dark:from-green-900/10 dark:to-gray-800
                                @elseif($contract->status === 'completed') border-blue-200 dark:border-blue-700 bg-gradient-to-r from-blue-50/30 to-white dark:from-blue-900/10 dark:to-gray-800
                                @elseif($contract->status === 'cancelled') border-red-200 dark:border-red-700 bg-gradient-to-r from-red-50/30 to-white dark:from-red-900/10 dark:to-gray-800
                                @else border-yellow-200 dark:border-yellow-700 bg-gradient-to-r from-yellow-50/30 to-white dark:from-yellow-900/10 dark:to-gray-800 @endif
                                transition-all duration-200 transform group-hover:scale-105 overflow-hidden">
                                
                                <!-- Status Strip -->
                                <div class="h-1 w-full 
                                    @if($contract->status === 'active') bg-gradient-to-r from-green-400 to-green-600
                                    @elseif($contract->status === 'completed') bg-gradient-to-r from-blue-400 to-blue-600
                                    @elseif($contract->status === 'cancelled') bg-gradient-to-r from-red-400 to-red-600
                                    @else bg-gradient-to-r from-yellow-400 to-yellow-600 @endif"></div>
                                
                                <!-- Contract Header -->
                                <div class="p-4 pb-2 flex-grow">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                                @if($contract->employer_id === auth()->id())
                                                    {{ substr($contract->contractor->name, 0, 1) }}
                                                @else
                                                    {{ substr($contract->employer->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div class="ml-3 flex-1 min-w-0">
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                                                    @if($contract->employer_id === auth()->id())
                                                        {{ $contract->contractor->name }}
                                                    @else
                                                        {{ $contract->employer->name }}
                                                    @endif
                                                </h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ ucfirst($contract->contract_type) }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Compact Status Badge -->
                                        <div class="flex-shrink-0">
                                            <div class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold
                                                @if($contract->status === 'active') bg-green-500 text-white
                                                @elseif($contract->status === 'completed') bg-blue-500 text-white
                                                @elseif($contract->status === 'cancelled') bg-red-500 text-white
                                                @else bg-yellow-500 text-white @endif">
                                                @if($contract->status === 'active')
                                                    <div class="w-1.5 h-1.5 bg-white rounded-full mr-1 animate-pulse"></div>
                                                    ACTIVE
                                                @elseif($contract->status === 'completed')
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    COMPLETED
                                                @elseif($contract->status === 'cancelled')
                                                    ✕ CANCELLED
                                                @else
                                                    {{ strtoupper($contract->status) }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Rate Info - Compact -->
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-md px-3 py-1.5">
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Rate</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $contract->formatted_rate }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Content Area - Smaller Fixed Height -->
                                    <div class="h-8 flex flex-col justify-start">
                                        @if($contract->jobPost)
                                            <div class="text-xs text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 rounded px-2 py-1 truncate">
                                                🔗 {{ $contract->jobPost->title }}
                                            </div>
                                        @elseif($contract->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                {{ Str::limit($contract->description, 60) }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 dark:text-gray-500 italic">No details</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Contract Stats - Compact -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 bg-green-100 dark:bg-green-900/20 rounded-md flex items-center justify-center mr-2">
                                                <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Earned</p>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format($contract->total_earned, 2) }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900/20 rounded-md flex items-center justify-center mr-2">
                                                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Hours</p>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $contract->hours_worked }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hover Effect Indicator -->
                                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
</div>

</x-layouts.app>
