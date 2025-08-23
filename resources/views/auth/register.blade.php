<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnlyVerified - Join Premium Talent Platform</title>
    @vite(['resources/themes/anchor/assets/css/app.css', 'resources/themes/anchor/assets/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in {
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        /* Button fixes */
        .btn-clickable {
            pointer-events: auto !important;
            cursor: pointer !important;
            user-select: none;
        }
        .btn-clickable:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }
        
        /* User Type Selection Styles */
        .user-type-card.selected {
            border-color: #6366f1 !important;
            background-color: #f8fafc;
        }
        
        .user-type-card.selected .user-type-radio {
            border-color: #6366f1;
        }
        
        .user-type-card.selected .selected-indicator {
            display: block !important;
        }
    </style>
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-full flex">
        <!-- Left Panel - Branding -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 gradient-bg">
            <div class="mx-auto w-full max-w-sm lg:w-96 text-white relative">
                <div class="fade-in">
                    <!-- Logo -->
                    <div class="flex items-center mb-12">
                        <div class="flex items-center">
                            <img src="{{ asset('images/onlyverified-logo.svg') }}" alt="OnlyVerified" class="h-16 w-auto filter brightness-0 invert">
                        </div>
                        <div class="ml-4">
                            <p class="text-white/80 text-sm">Premium Talent Platform</p>
                        </div>
                    </div>

                    <!-- Hero Content -->
                    <div class="mb-12">
                        <h2 class="text-4xl font-bold mb-4 leading-tight">
                            Join the 
                            <span class="text-yellow-300">Elite Network</span>
                            of Professionals
                        </h2>
                        <p class="text-xl text-white/90 mb-8">
                            Get verified, connect with top agencies, and unlock premium opportunities in the adult content industry.
                        </p>
                        
                        <!-- Benefits -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3 slide-in">
                                <div class="w-6 h-6 bg-green-400 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-white/90">Get verified in 24-48 hours</span>
                            </div>
                            <div class="flex items-center space-x-3 slide-in" style="animation-delay: 0.2s;">
                                <div class="w-6 h-6 bg-blue-400 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="text-white/90">Connect with verified agencies</span>
                            </div>
                            <div class="flex items-center space-x-3 slide-in" style="animation-delay: 0.4s;">
                                <div class="w-6 h-6 bg-purple-400 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                                <span class="text-white/90">Access premium job opportunities</span>
                            </div>
                            <div class="flex items-center space-x-3 slide-in" style="animation-delay: 0.6s;">
                                <div class="w-6 h-6 bg-pink-400 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <span class="text-white/90">100% secure and private</span>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="glass-effect rounded-2xl p-6">
                        <h3 class="text-lg font-semibold mb-4">Trusted by Industry Leaders</h3>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-yellow-300">500+</div>
                                <div class="text-xs text-white/70">Verified Agencies</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-300">2.5K+</div>
                                <div class="text-xs text-white/70">Active Talents</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-300">$2M+</div>
                                <div class="text-xs text-white/70">Monthly Earnings</div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Elements -->
                    <div class="absolute top-20 left-10 w-4 h-4 bg-white/20 rounded-full floating-animation"></div>
                    <div class="absolute top-40 right-10 w-6 h-6 bg-yellow-300/30 rounded-full floating-animation" style="animation-delay: 1s;"></div>
                    <div class="absolute bottom-40 left-16 w-3 h-3 bg-pink-300/30 rounded-full floating-animation" style="animation-delay: 2s;"></div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Registration Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-28 bg-white">
            <div class="mx-auto w-full max-w-md">
                <div class="fade-in">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Create your premium account</h2>
                        <p class="text-gray-600">Join thousands of verified professionals</p>
                        
                        <!-- Progress Indicator -->
                        <div class="flex items-center justify-center mt-6 mb-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">1</div>
                                <div class="w-8 h-1 bg-gray-200 rounded-full"></div>
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-sm font-semibold">2</div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Step 1 of 2: Account Information</p>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form class="space-y-5" action="{{ route('custom.register.post') }}" method="POST">
                        @csrf
                        
                        <!-- Name Field -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input id="name" name="name" type="text" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-400" 
                                       placeholder="Enter your full name" value="{{ old('name') }}">
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-400" 
                                       placeholder="Enter your email address" value="{{ old('email') }}">
                            </div>
                        </div>

                        <!-- Phone Field -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number 
                                <span class="text-gray-500 font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input id="phone_number" name="phone_number" type="tel" 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-400" 
                                       placeholder="Enter your phone number" value="{{ old('phone_number') }}">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Helps prevent duplicate accounts and improves security</p>
                        </div>

                        <!-- User Type Field -->
                        <div>
                            <label for="user_type_id" class="block text-sm font-medium text-gray-700 mb-3">Choose Your Account Type *</label>
                            
                            <!-- User Type Selection -->
                            <div class="mb-4">
                                <!-- Hiring Entities Section -->
                                <div class="mb-4">
                                    <div class="flex items-center mb-2">
                                        <div class="w-4 h-4 bg-purple-100 rounded flex items-center justify-center mr-2">
                                            <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-purple-800">Can Post Jobs & Hire Talent</span>
                                    </div>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($userTypes->where('can_hire', true) as $userType)
                                        <div class="user-type-card cursor-pointer border border-gray-200 rounded-lg p-2 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200" 
                                             data-type="{{ $userType->id }}" 
                                             onclick="selectUserType({{ $userType->id }})">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 bg-purple-100 rounded flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900">{{ $userType->display_name }}</span>
                                                </div>
                                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full flex items-center justify-center user-type-radio">
                                                    <div class="w-2 h-2 bg-indigo-600 rounded-full hidden selected-indicator"></div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Professionals Section -->
                                <div>
                                    <div class="flex items-center mb-2">
                                        <div class="w-4 h-4 bg-blue-100 rounded flex items-center justify-center mr-2">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-blue-800">Can Apply to Jobs & Get Featured</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($userTypes->where('can_hire', false) as $userType)
                                        <div class="user-type-card cursor-pointer border border-gray-200 rounded-lg p-2 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200" 
                                             data-type="{{ $userType->id }}" 
                                             onclick="selectUserType({{ $userType->id }})">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs font-medium text-gray-900">{{ $userType->display_name }}</span>
                                                </div>
                                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full flex items-center justify-center user-type-radio">
                                                    <div class="w-2 h-2 bg-indigo-600 rounded-full hidden selected-indicator"></div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden input for form submission -->
                            <input type="hidden" id="user_type_id" name="user_type_id" value="{{ old('user_type_id') }}" required>
                            
                            <!-- Important Note -->
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mt-3">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-amber-800">
                                            <strong>Important:</strong> Your account type cannot be changed after registration. Choose carefully based on your intended use.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-400" 
                                       placeholder="Enter password (min 8 characters)">
                            </div>
                        </div>

                        <!-- Confirm Password Field -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-400" 
                                       placeholder="Confirm your password">
                            </div>
                        </div>

                        <!-- Terms & Privacy -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terms" name="terms" type="checkbox" required
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="text-gray-700">
                                        I agree to OnlyVerified's 
                                        <a href="{{ route('terms-of-service') }}" target="_blank" class="text-indigo-600 hover:text-indigo-500 font-medium">Terms of Service</a> 
                                        and 
                                        <a href="{{ route('privacy-policy') }}" target="_blank" class="text-indigo-600 hover:text-indigo-500 font-medium">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" 
                                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                </span>
                                Create My Premium Account
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="mt-6">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300" />
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-4 bg-white text-gray-500">Already have an account?</span>
                                </div>
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('custom.login') }}" 
                                   class="w-full inline-block relative py-3 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:border-gray-400 hover:shadow-md transform hover:-translate-y-0.5 group cursor-pointer"
                                   style="z-index: 10; position: relative; display: block; text-decoration: none; text-align: center;">
                                    <div class="flex items-center justify-center">
                                        <svg class="h-5 w-5 text-gray-500 mr-2 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        Sign in to your account
                                    </div>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function selectUserType(typeId) {
            // Remove selection from all cards
            document.querySelectorAll('.user-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selection to clicked card
            const selectedCard = document.querySelector(`[data-type="${typeId}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
            
            // Set the hidden input value
            document.getElementById('user_type_id').value = typeId;
        }
        
        // Initialize selection if there's an old value
        document.addEventListener('DOMContentLoaded', function() {
            const oldValue = document.getElementById('user_type_id').value;
            if (oldValue) {
                selectUserType(oldValue);
            }
        });
    </script>
</body>
</html>
