<x-theme::layouts.app>
<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
    <div id="react-messaging-app" class="h-screen">
        <!-- React messaging app will mount here -->
        <div class="flex h-full items-center justify-center">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600 dark:text-gray-400">Loading messages...</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass user data to React app
    window.userData = {
        id: {{ auth()->id() }},
        name: '{{ auth()->user()->name }}',
        email: '{{ auth()->user()->email }}',
        avatar: '{{ auth()->user()->avatar ?? '' }}'
    };
    
    // CSRF token for API requests
    window.csrfToken = '{{ csrf_token() }}';
    
    // API base URL
    window.apiBaseUrl = '{{ url("/api/marketplace") }}';
    
    // Current conversation ID if specified
    @if(isset($conversationId))
        window.initialConversationId = {{ $conversationId }};
    @endif
</script>

@vite(['resources/js/react-messaging-app.jsx'])


@push('styles')
<style>
    /* Remove default margins/padding for full-screen messaging */
    .app-layout {
        margin: 0;
        padding: 0;
    }
    
    #react-messaging-app {
        height: 100vh;
        overflow: hidden;
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        body {
            background-color: #18181b;
        }
    }
</style>
@endpush

</x-theme::layouts.app>
