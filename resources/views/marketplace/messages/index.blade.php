<x-layouts.app>
    <div id="messages-app" class="min-h-screen bg-gray-50">
        <!-- React Messaging App Mount Point -->
    </div>

    <!-- Pass data to React -->
    <script>
        window.Laravel = {
            ...window.Laravel,
            user: @json(auth()->user()),
            pusherKey: '{{ config("broadcasting.connections.pusher.key") }}',
            pusherCluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
            messagingData: {
                folders: @json($folders),
                conversations: @json($conversations),
                selectedConversation: @json($selectedConversation),
                messages: @json($messages)
            }
        };
    </script>

    @vite(['resources/js/messaging-app.js'])
</x-layouts.app>
