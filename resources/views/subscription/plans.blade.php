<x-layouts.marketing>
    <!-- Hero Section -->
    <div class="relative min-h-screen bg-gradient-to-br from-slate-50 via-gray-100 to-slate-50">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%239C92AC\" fill-opacity=\"0.05\"%3E%3Ccircle cx=\"7\" cy=\"7\" r=\"1\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Header -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 bg-purple-500/10 rounded-full border border-purple-500/30 backdrop-blur-sm mb-6">
                    <span class="text-purple-700 text-sm font-medium">💎 Premium OnlyVerified Plans</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                    Choose Your Perfect Plan
                </h1>
                <p class="text-xl text-gray-700 max-w-3xl mx-auto mb-8">
                    Unlock the full potential of OnlyVerified with plans designed for every creator and agency. 
                    Start free, scale unlimited.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        No Setup Fees
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Cancel Anytime
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        30-Day Money Back
                    </div>
                </div>
            </div>

            <!-- Plans Grid -->
            @php
                // Use the actual plans from the database, but ensure unique plans
                $uniquePlans = collect($plans)->unique('name')->values();
                
                // Add highlight property for the "Pro" plan to make it stand out
                $planList = $uniquePlans->map(function ($plan) {
                    $planArray = is_array($plan) ? $plan : $plan->toArray();
                    $planArray['highlight'] = ($planArray['name'] === 'Pro'); // Highlight the Pro plan
                    return $planArray;
                });
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 max-w-7xl mx-auto">
                @foreach ($planList as $plan)
                    <div class="relative group">
                        @if($plan['highlight'])
                            <!-- Popular Badge -->
                            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-10">
                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-1 rounded-full text-sm font-semibold shadow-lg">
                                    🔥 Most Popular
                                </div>
                            </div>
                        @endif
                        
                        <div class="relative h-full bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200 overflow-hidden hover:bg-white/90 transition-all duration-300 group-hover:scale-105 {{ $plan['highlight'] ? 'ring-2 ring-purple-500 shadow-2xl shadow-purple-500/25' : 'shadow-xl' }}">
                            <!-- Plan Header -->
                            <div class="px-8 py-8 text-center">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan['name'] }}</h3>
                                <p class="text-gray-600 mb-6 text-sm">{{ $plan['description'] }}</p>
                                <div class="mb-6">
                                    <div class="flex items-baseline justify-center">
                                        <span class="text-5xl font-bold text-gray-900">${{ number_format($plan['price'], 0) }}</span>
                                        <span class="text-gray-600 ml-2">/month</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="px-8 pb-8">
                                <ul class="space-y-4">
                                    <li class="flex items-center text-sm">
                                        <div class="w-5 h-5 rounded-full bg-green-500/20 flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="text-gray-800">
                                            <strong class="text-blue-600">{{ $plan['job_post_limit'] ?? 'Unlimited' }}</strong> Job Posts
                                        </span>
                                    </li>
                                    <li class="flex items-center text-sm">
                                        <div class="w-5 h-5 rounded-full bg-green-500/20 flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="text-gray-800">
                                            <strong class="text-blue-600">{{ $plan['chat_application_limit'] ?? 'Unlimited' }}</strong> Applications
                                        </span>
                                    </li>
                                    <li class="flex items-center text-sm">
                                        <div class="w-5 h-5 rounded-full {{ $plan['unlimited_chats'] ? 'bg-green-500/20' : 'bg-gray-500/20' }} flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-3 h-3 {{ $plan['unlimited_chats'] ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="{{ $plan['unlimited_chats'] ? 'text-gray-800' : 'text-gray-500' }}">Unlimited Chats</span>
                                    </li>
                                    <li class="flex items-center text-sm">
                                        <div class="w-5 h-5 rounded-full {{ $plan['advanced_filters'] ? 'bg-green-500/20' : 'bg-gray-500/20' }} flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-3 h-3 {{ $plan['advanced_filters'] ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="{{ $plan['advanced_filters'] ? 'text-gray-800' : 'text-gray-500' }}">Advanced Filters</span>
                                    </li>
                                    <li class="flex items-center text-sm">
                                        <div class="w-5 h-5 rounded-full {{ $plan['analytics'] ? 'bg-green-500/20' : 'bg-gray-500/20' }} flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-3 h-3 {{ $plan['analytics'] ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="{{ $plan['analytics'] ? 'text-gray-800' : 'text-gray-500' }}">Analytics Dashboard</span>
                                    </li>
                                    <li class="flex items-center text-sm">
                                        <div class="w-5 h-5 rounded-full {{ $plan['priority_listings'] ? 'bg-green-500/20' : 'bg-gray-500/20' }} flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-3 h-3 {{ $plan['priority_listings'] ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="{{ $plan['priority_listings'] ? 'text-gray-800' : 'text-gray-500' }}">Priority Listings</span>
                                    </li>
                                    <li class="flex items-center text-sm">
                                        <div class="w-5 h-5 rounded-full {{ $plan['featured_status'] ? 'bg-green-500/20' : 'bg-gray-500/20' }} flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-3 h-3 {{ $plan['featured_status'] ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="{{ $plan['featured_status'] ? 'text-gray-800' : 'text-gray-500' }}">Featured Status</span>
                                    </li>
                                </ul>
                                
                                <!-- Action Button -->
                                <div class="mt-8">
                                    @if (isset($currentStats) && $currentStats['plan_name'] === $plan['name'])
                                        <div class="w-full py-3 px-4 text-center border border-purple-500 rounded-xl text-purple-700 font-medium bg-purple-100">
                                            ✨ Current Plan
                                        </div>
                                    @elseif($user && isset($currentStats) && $currentStats['has_subscription'])
                                        <button onclick="showPlanPreview({{ $plan['id'] }}, '{{ $plan['name'] }}', {{ $plan['price'] }})" class="w-full py-3 px-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl font-semibold hover:from-purple-600 hover:to-pink-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                            Change Plan
                                        </button>
                                    @else
                                        @if ($plan['price'] > 0)
                                            <div class="space-y-3">
                                                <form method="POST" action="{{ route('subscription.subscribe') }}">
                                                    @csrf
                                                    <input type="hidden" name="plan_id" value="{{ $plan['id'] }}">
                                                    <button type="submit" class="w-full py-3 px-4 {{ $plan['highlight'] ? 'bg-gradient-to-r from-purple-500 to-pink-500 shadow-lg shadow-purple-500/25' : 'bg-gradient-to-r from-blue-500 to-purple-500' }} text-white rounded-xl font-semibold hover:scale-105 transform transition-all duration-200">
                                                        💳 Pay with Card
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('subscription.payment', ['plan' => $plan['id']]) }}">
                                                    @csrf
                                                    <input type="hidden" name="payment_method" value="crypto">
                                                    <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-orange-500 to-yellow-500 text-white rounded-xl font-semibold hover:from-orange-600 hover:to-yellow-600 transform hover:scale-105 transition-all duration-200">
                                                        ₿ Pay with Crypto
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('subscription.subscribe') }}">
                                                @csrf
                                                <input type="hidden" name="plan_id" value="{{ $plan['id'] }}">
                                                <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl font-semibold hover:from-green-600 hover:to-emerald-600 transform hover:scale-105 transition-all duration-200">
                                                    🚀 Get Started Free
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- FAQ Section -->
            <div class="mt-20 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-purple-500/10 rounded-full border border-purple-500/30 backdrop-blur-sm mb-6">
                    <span class="text-purple-700 text-sm font-medium">💡 Common Questions</span>
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-12">Frequently Asked Questions</h2>
                <div class="max-w-4xl mx-auto">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200 p-8 text-left hover:bg-white/90 transition-all duration-300 shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">Plan Changes</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">Can I change my plan anytime? Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately with prorated billing.</p>
                        </div>
                        
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200 p-8 text-left hover:bg-white/90 transition-all duration-300 shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-yellow-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8.070 8.340 8.433 7.418zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.364.243 0 .697-.155.103-.346.196-.567.267z"></path>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 5a1 1 0 012 0v.092a4.535 4.535 0 011.676.662C13.398 6.28 14 7.36 14 8.5c0 1.441-.793 2.307-1.676 2.746A4.535 4.535 0 0111 11.908V12a1 1 0 11-2 0v-.092a4.535 4.535 0 01-1.676-.662C6.602 10.72 6 9.64 6 8.5c0-1.441.793-2.307 1.676-2.746A4.535 4.535 0 019 5.092V5z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">Crypto Payments</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">What cryptocurrencies do you accept? We accept Bitcoin (BTC), Ethereum (ETH), USDT, and USDC for secure, private subscription payments.</p>
                        </div>
                        
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200 p-8 text-left hover:bg-white/90 transition-all duration-300 shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">Money-Back Guarantee</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">Is there a refund policy? Yes, we offer a 30-day money-back guarantee for all paid plans. No questions asked, full refund.</p>
                        </div>
                        
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200 p-8 text-left hover:bg-white/90 transition-all duration-300 shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">24/7 Support</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed">Need help? Our premium support team is available 24/7 via chat, email, and priority channels for all subscribers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Preview Modal -->
    <div id="planPreviewModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
            <div class="bg-white rounded-lg sm:rounded-xl shadow-2xl w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl 2xl:max-w-2xl h-[95vh] sm:h-auto sm:max-h-[85vh] flex flex-col">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-3 sm:p-4 md:p-6 border-b border-gray-200 flex-shrink-0">
                    <div class="flex items-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                            <svg class="h-4 w-4 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">Plan Preview</h3>
                    </div>
                    <button onclick="closePlanPreview()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Content (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6" style="max-height: calc(100vh - 200px);">
                    <div id="previewContent" class="text-sm text-gray-600">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-6 w-6 sm:h-8 sm:w-8 border-b-2 border-blue-600"></div>
                            <p class="ml-3 text-gray-600 text-sm sm:text-base">Loading preview...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer (Always Visible) -->
                <div class="p-3 sm:p-4 md:p-6 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                    <!-- Mobile Layout -->
                    <div class="flex flex-col items-center gap-3 sm:hidden">
                        <button onclick="confirmPlanChange()" type="button" class="confirm-btn w-full max-w-sm inline-flex items-center justify-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Confirm Change
                        </button>
                        <button onclick="closePlanPreview()" type="button" class="w-full max-w-sm inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                    </div>
                    
                    <!-- Desktop Layout -->
                    <div class="hidden sm:flex sm:justify-end sm:items-center gap-3">
                        <button onclick="closePlanPreview()" type="button" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                        <button onclick="confirmPlanChange()" type="button" class="confirm-btn inline-flex items-center justify-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Confirm Change
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedPlanId = null;
        let selectedPlanName = null;
        let selectedPlanPrice = null;

        async function showPlanPreview(planId, planName, planPrice) {
            selectedPlanId = planId;
            selectedPlanName = planName;
            selectedPlanPrice = planPrice;
            
            const modal = document.getElementById('planPreviewModal');
            const previewContent = document.getElementById('previewContent');
            
            // Show loading state
            previewContent.innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="ml-4 text-gray-600 text-lg">Loading plan preview...</p>
                </div>
            `;
            
            // Show modal
            modal.classList.remove('hidden');
            
            // Fetch preview data
            try {
                const response = await fetch(`{{ route('subscription.plan.preview') }}?plan_id=${planId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                // Build preview content with perfect styling
                let content = buildPreviewContent(data);
                
                previewContent.innerHTML = content;
                
                // Update confirm button
                updateConfirmButton(data);
                
            } catch (error) {
                console.error('Error fetching plan preview:', error);
                previewContent.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <p class="text-red-600 text-lg font-medium">Error loading preview</p>
                        <p class="text-gray-500 mt-2">Please try again or contact support if the issue persists.</p>
                        <button onclick="showPlanPreview(${planId}, '${planName}', ${planPrice})" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                            Retry
                        </button>
                    </div>
                `;
            }
        }
        
        function buildPreviewContent(data) {
            let content = `<div class="space-y-3 sm:space-y-4">`;
            
            // Plan comparison section
            content += `
                <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-slate-200">
                    <div class="flex items-center mb-2 sm:mb-3">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 bg-blue-500 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-sm sm:text-base md:text-lg font-bold text-slate-800">Plan Comparison</h4>
                    </div>
            `;
            
            if (data.current_plan) {
                content += `
                    <div class="flex justify-between items-center p-2 sm:p-3 md:p-4 bg-white rounded-md sm:rounded-lg shadow-sm mb-2 sm:mb-3 border border-slate-200">
                        <span class="text-xs sm:text-sm font-semibold text-slate-600">Current:</span>
                        <span class="text-sm sm:text-base font-bold text-slate-800 bg-slate-100 px-2 sm:px-3 py-1 rounded-full">${data.current_plan}</span>
                    </div>
                `;
            }
            
            content += `
                    <div class="flex justify-between items-center p-2 sm:p-3 md:p-4 bg-blue-50 rounded-md sm:rounded-lg border-2 border-blue-200">
                        <span class="text-xs sm:text-sm font-semibold text-blue-700">New:</span>
                        <span class="text-sm sm:text-base font-bold text-blue-900 bg-blue-100 px-2 sm:px-3 py-1 rounded-full">${data.new_plan}</span>
                    </div>
                </div>
            `;
            
            // Billing information section
            content += `
                <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-emerald-200">
                    <div class="flex items-center mb-2 sm:mb-3">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 bg-emerald-500 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h4 class="text-sm sm:text-base md:text-lg font-bold text-emerald-800">Billing</h4>
                    </div>
                    <div class="flex justify-between items-center p-2 sm:p-3 md:p-4 bg-white rounded-md sm:rounded-lg shadow-sm border border-emerald-200">
                        <span class="text-xs sm:text-sm font-semibold text-slate-600">Charge:</span>
                        <span class="text-lg sm:text-xl md:text-2xl font-bold text-emerald-600">$${data.immediate_charge || '0'}</span>
                    </div>
            `;
            
            if (data.remaining_days && data.remaining_days > 0) {
                content += `
                    <div class="flex justify-between items-center p-2 sm:p-3 md:p-4 bg-amber-50 rounded-md sm:rounded-lg border border-amber-200 mt-2 sm:mt-3">
                        <span class="text-xs sm:text-sm font-semibold text-amber-700">Remaining:</span>
                        <span class="text-sm sm:text-base font-bold text-amber-800">${data.remaining_days} days</span>
                    </div>
                `;
            }
            
            content += `</div>`;
            
            // Features gained section
            if (data.features_gained && data.features_gained.length > 0) {
                content += `
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-green-200">
                        <div class="flex items-center mb-2 sm:mb-3">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 bg-green-500 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm sm:text-base md:text-lg font-bold text-green-800">You'll Gain</h4>
                        </div>
                        <div class="space-y-1 sm:space-y-2">
                `;
                
                data.features_gained.forEach(feature => {
                    content += `
                        <div class="flex items-center p-2 sm:p-3 bg-white rounded-md sm:rounded-lg shadow-sm border border-green-200">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs sm:text-sm font-medium text-green-700">${feature}</span>
                        </div>
                    `;
                });
                
                content += `</div></div>`;
            }
            
            // Features lost section
            if (data.features_lost && data.features_lost.length > 0) {
                content += `
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-red-200">
                        <div class="flex items-center mb-2 sm:mb-3">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 bg-red-500 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm sm:text-base md:text-lg font-bold text-red-800">You'll Lose</h4>
                        </div>
                        <div class="space-y-1 sm:space-y-2">
                `;
                
                data.features_lost.forEach(feature => {
                    content += `
                        <div class="flex items-center p-2 sm:p-3 bg-white rounded-md sm:rounded-lg shadow-sm border border-red-200">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs sm:text-sm font-medium text-red-700">${feature}</span>
                        </div>
                    `;
                });
                
                content += `</div></div>`;
            }
            
            // Warnings section
            if (data.warnings && data.warnings.length > 0) {
                content += `
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-yellow-300">
                        <div class="flex items-center mb-2 sm:mb-3">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 bg-yellow-500 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm sm:text-base md:text-lg font-bold text-yellow-800">Warnings</h4>
                        </div>
                        <div class="space-y-1 sm:space-y-2">
                `;
                
                data.warnings.forEach(warning => {
                    content += `
                        <div class="flex items-start p-2 sm:p-3 bg-white rounded-md sm:rounded-lg shadow-sm border border-yellow-200">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 mt-0.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs sm:text-sm font-medium text-yellow-800">${warning}</span>
                        </div>
                    `;
                });
                
                content += `</div></div>`;
            }
            
            content += `</div>`;
            return content;
        }
        
        function updateConfirmButton(data) {
            const confirmBtns = document.querySelectorAll('.confirm-btn');
            const buttonText = data.type === 'upgrade' ? 'Upgrade Plan' : 
                              data.type === 'downgrade' ? 'Downgrade Plan' : 'Change Plan';
            
            confirmBtns.forEach(btn => {
                btn.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    ${buttonText}
                `;
            });
        }
        
        function closePlanPreview() {
            document.getElementById('planPreviewModal').classList.add('hidden');
        }
        
        function confirmPlanChange() {
            if (selectedPlanId) {
                // Disable all confirm buttons to prevent double-clicks
                const confirmBtns = document.querySelectorAll('.confirm-btn');
                confirmBtns.forEach(btn => {
                    btn.disabled = true;
                    btn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    `;
                });
                
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = selectedPlanPrice > 0 ? '{{ route('subscription.upgrade') }}' : '{{ route('subscription.downgrade') }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const planIdInput = document.createElement('input');
                planIdInput.type = 'hidden';
                planIdInput.name = 'plan_id';
                planIdInput.value = selectedPlanId;
                form.appendChild(planIdInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Close modal when clicking outside
        document.getElementById('planPreviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePlanPreview();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePlanPreview();
            }
        });
    </script>
</x-layouts.marketing>
