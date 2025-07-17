<x-layouts.app>
    <x-app.container>
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Notifications</h1>
                    <p class="text-gray-600 dark:text-gray-400">Stay updated with your latest activities</p>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="ti ti-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Activity</h2>
                    <button onclick="markAllAsRead()" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        Mark all as read
                    </button>
                </div>
            </div>

            <div id="notifications-container" class="divide-y divide-gray-200 dark:divide-zinc-700">
                <!-- Notifications will be loaded here dynamically -->
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <i class="ti ti-loader-2 animate-spin text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Loading notifications...</p>
                    </div>
                </div>
            </div>
        </div>
    </x-app.container>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();
        });

        function loadNotifications() {
            fetch('{{ route("notifications.recent-activity") }}')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('notifications-container');
                    
                    if (data.length === 0) {
                        container.innerHTML = `
                            <div class="flex items-center justify-center py-12">
                                <div class="text-center">
                                    <i class="ti ti-bell-off text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">No notifications yet</p>
                                </div>
                            </div>
                        `;
                        return;
                    }

                    container.innerHTML = data.map(notification => `
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors duration-200 ${notification.read_at ? '' : 'bg-blue-50 dark:bg-blue-900/20'}">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="ti ti-bell text-blue-600 dark:text-blue-400 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        ${notification.data.title || 'Notification'}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        ${notification.data.message || notification.data.body}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                        ${new Date(notification.created_at).toLocaleString()}
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    ${!notification.read_at ? `
                                        <button onclick="markAsRead('${notification.id}')" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            Mark as read
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    const container = document.getElementById('notifications-container');
                    container.innerHTML = `
                        <div class="flex items-center justify-center py-12">
                            <div class="text-center">
                                <i class="ti ti-alert-circle text-4xl text-red-400 dark:text-red-500 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">Error loading notifications</p>
                            </div>
                        </div>
                    `;
                });
        }

        function markAsRead(notificationId) {
            fetch(`{{ url('/api/notifications/mark-read') }}/${notificationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications(); // Reload notifications
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        function markAllAsRead() {
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications(); // Reload notifications
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        }
    </script>
    @endpush
</x-layouts.app>
