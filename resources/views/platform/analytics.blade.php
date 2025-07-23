<x-layouts.app>

<x-app.container>
<div class="bg-gray-50 dark:bg-zinc-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">OnlyFans Ecosystem Analytics</h1>
            <p class="text-gray-600 dark:text-gray-400">Real-time insights and performance metrics for the professional marketplace</p>
        </div>
        
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Jobs</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_jobs']) }}</p>
                        <p class="text-sm text-green-600 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414L10 1.586l4.707 4.707a1 1 0 11-1.414 1.414L10 4.414 6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                12% from last month
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Jobs</p>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($stats['active_jobs']) }}</p>
                        <p class="text-sm text-green-600 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414L10 1.586l4.707 4.707a1 1 0 11-1.414 1.414L10 4.414 6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                8% from last month
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Applications</p>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['total_applications']) }}</p>
                        <p class="text-sm text-green-600 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414L10 1.586l4.707 4.707a1 1 0 11-1.414 1.414L10 4.414 6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                24% from last month
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Success Rate</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['success_rate'] }}%</p>
                        <p class="text-sm text-green-600 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414L10 1.586l4.707 4.707a1 1 0 11-1.414 1.414L10 4.414 6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                3% from last month
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Earnings Trend -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Earnings Trend</h3>
                <div class="h-64">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
            
            <!-- Top Markets -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top Markets</h3>
                <div class="space-y-4">
                    @foreach($stats['top_markets'] as $market)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($market->market) }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-500 mr-2">{{ $market->total }} jobs</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($market->total / $stats['top_markets']->max('total')) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- User Growth Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">User Growth (Last 30 Days)</h3>
            <div class="h-64">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
        
        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Response Time</h4>
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ $stats['avg_response_time'] }}h</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Average response time</p>
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-blue-700">15% faster than industry average</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Platform Activity</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Online Users</span>
                        <span class="text-sm font-semibold text-green-600">342</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Active Sessions</span>
                        <span class="text-sm font-semibold text-blue-600">1,247</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Messages Today</span>
                        <span class="text-sm font-semibold text-purple-600">2,156</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quality Metrics</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Avg. Rating</span>
                        <span class="text-sm font-semibold text-yellow-600">4.8/5</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</span>
                        <span class="text-sm font-semibold text-green-600">94%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Satisfaction</span>
                        <span class="text-sm font-semibold text-blue-600">98%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Real-time Activity Feed -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Real-time Activity</h3>
            <div id="activityFeed" class="space-y-4">
                <!-- Activity items will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Earnings Trend Chart
    const earningsCtx = document.getElementById('earningsChart').getContext('2d');
    const earningsChart = new Chart(earningsCtx, {
        type: 'line',
        data: {
            labels: @json(array_column($stats['earnings_trend'], 'month')),
            datasets: [{
                label: 'Earnings ($)',
                data: @json(array_column($stats['earnings_trend'], 'earnings')),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthChart = new Chart(userGrowthCtx, {
        type: 'bar',
        data: {
            labels: @json($stats['user_growth']->pluck('date')),
            datasets: [{
                label: 'New Users',
                data: @json($stats['user_growth']->pluck('count')),
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Real-time activity feed
    function updateActivityFeed() {
        const activities = [
            { type: 'job', message: 'New job posted: "Chat Support for OnlyFans Model"', time: '2 mins ago', icon: '💼' },
            { type: 'application', message: 'Application submitted for "Customer Chat Representative"', time: '5 mins ago', icon: '📄' },
            { type: 'message', message: 'New message between Agency and Chatter', time: '8 mins ago', icon: '💬' },
            { type: 'user', message: 'New user registered: Professional Chatter', time: '12 mins ago', icon: '👤' },
            { type: 'success', message: 'Job completed: "Live Chat Support - Gaming"', time: '15 mins ago', icon: '✅' }
        ];
        
        const feed = document.getElementById('activityFeed');
        feed.innerHTML = activities.map(activity => `
            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                <div class="text-2xl">${activity.icon}</div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">${activity.message}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${activity.time}</p>
                </div>
            </div>
        `).join('');
    }
    
    // Initial load and periodic updates
    updateActivityFeed();
    setInterval(updateActivityFeed, 30000); // Update every 30 seconds
});
</script>

</x-app.container>
</x-layouts.app>
