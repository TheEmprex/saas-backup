<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Authentication Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">🔐 Authentication System Test</h1>
        
        <!-- Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold mb-2">Authentication Status</h3>
                <p class="{{ auth()->check() ? 'success' : 'error' }}">
                    {{ auth()->check() ? '✅ Authenticated' : '❌ Not Authenticated' }}
                </p>
                @if(auth()->check())
                    <p class="text-sm text-gray-600 mt-2">
                        User: {{ auth()->user()->name }}<br>
                        Email: {{ auth()->user()->email }}
                    </p>
                @endif
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold mb-2">Database Status</h3>
                <p class="success">✅ Users: {{ \App\Models\User::count() }}</p>
                <p class="success">✅ User Types: {{ \App\Models\UserType::count() }}</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold mb-2">Session Status</h3>
                <p class="success">✅ CSRF Token: {{ substr(csrf_token(), 0, 10) }}...</p>
                <p class="success">✅ Session ID: {{ session()->getId() ? substr(session()->getId(), 0, 10) . '...' : 'None' }}</p>
            </div>
        </div>

        <!-- Test Forms -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Login Test -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">🔑 Login Test</h2>
                
                <!-- Quick Demo Login -->
                <form action="{{ route('custom.login.post') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="email" value="admin@example.com">
                    <input type="hidden" name="password" value="password">
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        🚀 Quick Login (admin@example.com)
                    </button>
                </form>

                <!-- Manual Login Form -->
                <form action="{{ route('custom.login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('email') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Login
                    </button>
                </form>
            </div>

            <!-- Register Test -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">📝 Register Test</h2>
                
                <form action="{{ route('custom.register.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('name') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('email') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number (Optional)</label>
                        <input type="tel" name="phone_number" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('phone_number') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">User Type</label>
                        <select name="user_type_id" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select User Type</option>
                            @foreach(\App\Models\UserType::all() as $userType)
                                <option value="{{ $userType->id }}" {{ old('user_type_id') == $userType->id ? 'selected' : '' }}>
                                    {{ $userType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                        Register
                    </button>
                </form>
            </div>
        </div>

        <!-- Error Display -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-8">
                <h3 class="text-lg font-semibold text-red-800 mb-2">❌ Errors:</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Success Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-8">
                <h3 class="text-lg font-semibold text-green-800 mb-2">✅ Success:</h3>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Navigation Links -->
        <div class="bg-white p-6 rounded-lg shadow mt-8">
            <h3 class="text-lg font-semibold mb-4">🧭 Navigation</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('custom.login') }}" class="bg-blue-100 text-blue-800 px-3 py-2 rounded text-center hover:bg-blue-200">
                    Login Page
                </a>
                <a href="{{ route('custom.register') }}" class="bg-green-100 text-green-800 px-3 py-2 rounded text-center hover:bg-green-200">
                    Register Page
                </a>
                <a href="{{ route('marketplace.index') }}" class="bg-purple-100 text-purple-800 px-3 py-2 rounded text-center hover:bg-purple-200">
                    Marketplace
                </a>
                @auth
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-full bg-red-100 text-red-800 px-3 py-2 rounded hover:bg-red-200">
                        Logout
                    </button>
                </form>
                @endauth
            </div>
        </div>

        <!-- Debug Info -->
        <div class="bg-gray-50 p-6 rounded-lg mt-8">
            <h3 class="text-lg font-semibold mb-4">🔍 Debug Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong>Laravel Version:</strong> {{ app()->version() }}<br>
                    <strong>PHP Version:</strong> {{ PHP_VERSION }}<br>
                    <strong>Environment:</strong> {{ app()->environment() }}<br>
                    <strong>Debug Mode:</strong> {{ config('app.debug') ? 'Enabled' : 'Disabled' }}<br>
                </div>
                <div>
                    <strong>Session Driver:</strong> {{ config('session.driver') }}<br>
                    <strong>Session Lifetime:</strong> {{ config('session.lifetime') }} minutes<br>
                    <strong>CSRF Token Length:</strong> {{ strlen(csrf_token()) }}<br>
                    <strong>Current Time:</strong> {{ now()->format('Y-m-d H:i:s') }}<br>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some basic form validation feedback
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '⏳ Processing...';
                        submitBtn.disabled = true;
                    }
                });
            });
        });
    </script>
</body>
</html>
