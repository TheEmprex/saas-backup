@extends('theme::app')

@section('title', 'Messaging')

@section('content')
<!-- Vue Messaging App -->
<div id="messaging-app" class="min-h-screen bg-gray-50">
    <!-- The Vue messaging component will be mounted here -->
</div>

<!-- Pass data to Vue -->
<script>
    window.Laravel = {
        ...window.Laravel,
        user: @json($user),
        token: @json($token),
        messaging: {
            token: @json($token),
            user: @json($user),
            apiEndpoints: {
                validateToken: '{{ route('api.messaging-app.validate-token') }}',
                refreshToken: '{{ route('messaging-app.refresh-token') }}'
            }
        }
    };
</script>

@vite(['resources/js/messaging-app.js'])
@endsection
