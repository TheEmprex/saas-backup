<x-layouts.app>
    <div id="vue-messaging-app" class="h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">
        <!-- Vue.js messaging app will be mounted here -->
    </div>
    
    <!-- Pass Laravel data to Vue -->
    <script>
        window.MessagingApp = {
            user: @json(auth()->user()),
            csrfToken: @json(csrf_token()),
            apiBaseUrl: @json(url('/api/messages')),
        };
    </script>
    
    @vite(['resources/js/messaging-app.js'])
</x-layouts.app>
