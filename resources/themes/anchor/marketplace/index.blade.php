@php
$seoData = [
'title'         => 'OnlyVerified - Professional Marketplace',
'description'   => 'Connect chatters with agencies. Secure, verified, and professional hub for the OnlyFans ecosystem.',
    'image'         => url('/og_image.png'),
    'type'          => 'website'
];
@endphp

<x-layouts.marketing :seo="$seoData">

<!-- Hero Section -->
<div class="relative overflow-hidden bg-gradient-to-br from-indigo-900 via-blue-900 to-purple-900">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KPGcgZmlsbD0iIzllYTNiYSIgZmlsbC1vcGFjaXR5PSIwLjEiPgo8Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSI0Ii8+CjwvZz4KPC9nPgo8L3N2Zz4=')] opacity-20"></div>
    
    <!-- Floating Elements -->
    <div class="absolute top-20 left-10 w-20 h-20 bg-blue-500 rounded-full opacity-20 animate-pulse"></div>
    <div class="absolute top-40 right-20 w-16 h-16 bg-purple-500 rounded-full opacity-20 animate-pulse" style="animation-delay: 1s"></div>
    <div class="absolute bottom-20 left-20 w-12 h-12 bg-indigo-400 rounded-full opacity-20 animate-pulse" style="animation-delay: 2s"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
            <!-- Badge -->
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/20 text-blue-200 text-sm font-medium mb-8">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Trusted by 1000+ professionals
            </div>
            
            <!-- Main Heading -->
            <h1 class="text-5xl lg:text-7xl font-bold mb-8 text-white">
                <span class="block">Welcome to</span>
                <span class="block bg-gradient-to-r from-yellow-400 via-orange-400 to-red-400 bg-clip-text text-transparent">
                    OnlyVerified
                </span>
            </h1>
            
            <!-- Subtitle -->
            <p class="text-xl lg:text-2xl mb-12 max-w-4xl mx-auto text-blue-100 leading-relaxed">
                Connect verified chatters with top agencies. Build your career with our secure, professional hub designed for the OnlyFans ecosystem.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                <a href="{{ route('register') }}" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-black bg-gradient-to-r from-yellow-400 to-orange-400 rounded-xl hover:from-yellow-300 hover:to-orange-300 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Get Started Free
                </a>
                <a href="#resources" class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-white/30 rounded-xl hover:bg-white/10 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Resources
                </a>
                <a href="#how-it-works" class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-white/30 rounded-xl hover:bg-white/10 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    Learn More
                </a>
            </div>
            
            <!-- Trust Indicators -->
            <div class="flex flex-wrap justify-center items-center gap-8 text-blue-200 text-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Verified Professionals
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    Secure Payments
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    24/7 Support
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div id="how-it-works" class="bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-20">
            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/20 rounded-full text-blue-700 text-sm font-medium mb-8">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Simple 3-Step Process
            </div>
            <h2 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                How It 
                <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Works</span>
            </h2>
            <p class="text-xl lg:text-2xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                Join OnlyVerified and transform your career with our streamlined process designed for success
            </p>
        </div>
        
        <!-- Steps -->
        <div class="grid lg:grid-cols-3 gap-12 lg:gap-16">
            <!-- Step 1 -->
            <div class="relative group">
                <!-- Connection Line -->
                <div class="hidden lg:block absolute top-24 left-full w-16 h-0.5 bg-gradient-to-r from-blue-200 to-transparent z-0"></div>
                
                <div class="bg-white rounded-3xl p-10 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 relative z-10">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 shadow-lg">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            1
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">Sign Up & Verify</h3>
                    <p class="text-gray-600 leading-relaxed text-center mb-6">
                        Create your professional account and complete our comprehensive verification process to ensure trust and security for all platform members.
                    </p>
                    <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            5 minutes
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-blue-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            100% Secure
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="relative group">
                <!-- Connection Line -->
                <div class="hidden lg:block absolute top-24 left-full w-16 h-0.5 bg-gradient-to-r from-green-200 to-transparent z-0"></div>
                
                <div class="bg-white rounded-3xl p-10 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 relative z-10">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 shadow-lg">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            2
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">Connect & Communicate</h3>
                    <p class="text-gray-600 leading-relaxed text-center mb-6">
                        Browse premium opportunities, connect with verified partners, and communicate securely through our encrypted messaging platform.
                    </p>
                    <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Instant matching
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-purple-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path>
                            </svg>
                            Secure chat
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="relative group">
                <div class="bg-white rounded-3xl p-10 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 relative z-10">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 shadow-lg">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            3
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">Work & Get Paid</h3>
                    <p class="text-gray-600 leading-relaxed text-center mb-6">
                        Complete projects professionally, build your stellar reputation, and receive fast, secure payments while growing your business network.
                    </p>
                    <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Weekly payouts
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Build reputation
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="text-center mt-20">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 inline-block">
                <h3 class="text-2xl font-bold text-white mb-4">Ready to Get Started?</h3>
                <p class="text-blue-100 mb-6 max-w-md">Join thousands of professionals already earning through OnlyVerified</p>
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Start Your Journey
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Trusted by Professionals Worldwide</h2>
            <p class="text-lg text-gray-600">Join thousands of successful chatters and agencies building their careers</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center group">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 mb-4 group-hover:shadow-lg transition-shadow duration-300">
                    <div class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">{{ $stats['total_jobs'] }}+</div>
                    <div class="text-gray-600 font-medium">Active Jobs</div>
                </div>
                <p class="text-sm text-gray-500">Fresh opportunities posted daily</p>
            </div>
            <div class="text-center group">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-8 mb-4 group-hover:shadow-lg transition-shadow duration-300">
                    <div class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2">{{ $stats['total_chatters'] }}+</div>
                    <div class="text-gray-600 font-medium">Verified Chatters</div>
                </div>
                    <p class="text-sm text-gray-500">Verified professionals</p>
            </div>
            <div class="text-center group">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-8 mb-4 group-hover:shadow-lg transition-shadow duration-300">
                    <div class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">{{ $stats['total_agencies'] }}+</div>
                    <div class="text-gray-600 font-medium">Active Agencies</div>
                </div>
                <p class="text-sm text-gray-500">Trusted agency partners</p>
            </div>
            <div class="text-center group">
                <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl p-8 mb-4 group-hover:shadow-lg transition-shadow duration-300">
                    <div class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-2">{{ $stats['jobs_filled'] }}+</div>
                    <div class="text-gray-600 font-medium">Jobs Completed</div>
                </div>
                <p class="text-sm text-gray-500">Successful partnerships</p>
            </div>
        </div>
        
        <!-- Additional Trust Metrics -->
        <div class="mt-16 bg-gradient-to-r from-gray-50 to-blue-50 rounded-3xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">100% Secure</h3>
                    <p class="text-gray-600">Bank-level encryption and verification for all professionals</p>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Fast Payments</h3>
                    <p class="text-gray-600">Instant payouts with multiple payment methods available</p>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Dedicated support team available around the clock</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Join As Section -->
<div class="bg-gradient-to-r from-slate-50 to-blue-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">Ready to Join?</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Choose your role and start building your career. Join thousands of professionals already earning through the OnlyFans ecosystem.</p>
        </div>
        
        <!-- Featured User Types -->
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <!-- Chatter -->
            <div class="bg-white rounded-2xl shadow-xl p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group flex flex-col h-full">
                <div class="text-center flex-1 flex flex-col">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Chatter</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">Individual professionals providing high-quality chatting services to OnlyVerified models and agencies. Perfect for freelancers looking to earn $15-50/hour.</p>
                    
                    <div class="mb-6 space-y-3">
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Flexible work hours
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Remote work opportunities
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            KYC verification required
                        </div>
                    </div>
                    
                    <div class="mt-auto">
                        <a href="{{ route('register') }}?type=chatter" class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Join as Chatter
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- OFM Agency -->
            <div class="bg-white rounded-2xl shadow-xl p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group flex flex-col h-full">
                <div class="text-center flex-1 flex flex-col">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">OFM Agency</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">OnlyVerified management agencies looking to hire skilled chatters for their models. Scale your operations with verified talent.</p>
                    
                    <div class="mb-6 space-y-3">
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Access to verified chatters
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Team management tools
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Earnings verification required
                        </div>
                    </div>
                    
                    <div class="mt-auto">
                        <a href="{{ route('register') }}?type=ofm_agency" class="block w-full bg-gradient-to-r from-green-600 to-teal-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-green-700 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Join as OFM Agency
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Chatting Agency -->
            <div class="bg-white rounded-2xl shadow-xl p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group flex flex-col h-full">
                <div class="text-center flex-1 flex flex-col">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Chatting Agency</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">Specialized agencies providing outsourced chatting services to agencies. Manage teams and deliver exceptional results.</p>
                    
                    <div class="mb-6 space-y-3">
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Bulk hiring capabilities
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Performance analytics
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Earnings verification required
                        </div>
                    </div>
                    
                    <div class="mt-auto">
                        <a href="{{ route('register') }}?type=chatting_agency" class="block w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Join as Chatting Agency
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional User Types Dropdown -->
        <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl mx-auto">
            <div class="text-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Looking for something else?</h3>
                <p class="text-gray-600">Explore all available roles and find your perfect match</p>
            </div>
            
            <div class="flex flex-col items-center">
                <div class="relative inline-block w-full max-w-md">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select your role:</label>
                        <div class="relative">
                            <select id="userTypeSelect" class="block w-full px-4 py-3 pr-10 text-base border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white shadow-sm">
                                <option value="">Choose your role...</option>
                                @foreach($userTypes as $userType)
                                    <option value="{{ $userType->name }}" data-description="{{ $userType->description }}">
                                        {{ $userType->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div id="roleDescription" class="text-sm text-gray-600 mb-6 min-h-[40px] transition-all duration-300"></div>
                    
                    <button id="joinButton" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" disabled>
                        Join Now
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('userTypeSelect');
        const description = document.getElementById('roleDescription');
        const button = document.getElementById('joinButton');
        
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const desc = selectedOption.getAttribute('data-description');
            
            if (this.value) {
                description.textContent = desc;
                description.style.opacity = '1';
                button.disabled = false;
            } else {
                description.textContent = '';
                description.style.opacity = '0';
                button.disabled = true;
            }
        });
        
        button.addEventListener('click', function() {
            const selectedType = select.value;
            if (selectedType) {
                window.location.href = `{{ route('register') }}?type=${selectedType}`;
            }
        });
    });
</script>

<!-- Featured Jobs Section -->
@if($featuredJobs->count() > 0)
<div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full text-black text-sm font-semibold mb-6">
                ⭐ Featured Opportunities
            </div>
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Premium Job Opportunities</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
                Discover hand-picked, high-quality positions from verified employers. These featured jobs offer the best opportunities for skilled professionals.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('marketplace.jobs.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Explore All Jobs
                </a>
                @guest
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Join OnlyVerified
                </a>
                @endguest
            </div>
        </div>
        
        <!-- Jobs Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($featuredJobs as $job)
            <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full">
                <!-- Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $job->title }}</h3>
                        <div class="text-sm text-gray-600 font-medium">{{ $job->user->name }}</div>
                    </div>
                    <span class="bg-gradient-to-r from-yellow-400 to-orange-400 text-black px-3 py-1 rounded-full text-xs font-bold flex-shrink-0 ml-2">
                        ⭐ Featured
                    </span>
                </div>
                
                <!-- Description -->
                <p class="text-gray-600 mb-6 line-clamp-3 flex-grow">{{ Str::limit($job->description, 120) }}</p>
                
                <!-- Details -->
                <div class="flex items-center justify-between mb-6">
                    <div class="text-sm text-gray-500">
                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded-md capitalize mr-2">
                            {{ $job->experience_level }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded-md capitalize">
                            {{ $job->market }}
                        </span>
                    </div>
                </div>
                
                <!-- Rate -->
                <div class="mb-6">
                    <div class="text-2xl font-bold text-green-600">
                        @if($job->rate_type === 'hourly' && $job->hourly_rate)
                            ${{ $job->hourly_rate }}/hr
                        @elseif($job->rate_type === 'fixed' && $job->fixed_rate)
                            ${{ $job->fixed_rate }}
                        @elseif($job->rate_type === 'commission' && $job->commission_percentage)
                            {{ $job->commission_percentage }}%
                        @else
                            Contact for rates
                        @endif
                    </div>
                </div>
                
                <!-- Action Button - Always at bottom -->
                <div class="mt-auto">
                    <a href="{{ route('jobs.show', $job->id) }}" 
                       class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-4 rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 text-center block shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        View Job Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Call to Action for New Users -->
        @guest
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">Ready to Find Your Perfect Job?</h3>
            <p class="text-lg mb-6 text-white">
                Join thousands of verified professionals already earning through OnlyVerified. 
                <strong class="text-yellow-300">Get access to exclusive opportunities</strong> and connect with top-tier employers.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Start Your Journey Today
                </a>
                <a href="{{ route('marketplace.jobs.index') }}" 
                   class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white rounded-xl font-bold hover:bg-white hover:text-indigo-600 transition-all duration-300">
                    Browse All {{ $stats['total_jobs'] ?? \App\Models\Job::count() }} Jobs
                </a>
            </div>
        </div>
        @endguest
    </div>
</div>
@endif

<!-- Resources Section -->
<div id="resources" class="bg-gradient-to-r from-purple-50 to-blue-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Platform Resources</h2>
            <p class="text-lg text-gray-600">Everything you need to succeed on OnlyVerified</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Getting Started Guide -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow group">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Getting Started Guide</h3>
                <p class="text-gray-600 mb-4">Step-by-step instructions for setting up your profile and getting verified on the platform.</p>
                <a href="{{ route('resources.getting-started') }}" class="text-blue-600 hover:text-blue-800 font-medium group-hover:underline">Read Guide →</a>
            </div>
            
            <!-- Best Practices -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow group">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Best Practices</h3>
                <p class="text-gray-600 mb-4">Tips and tricks for chatters and agencies to maximize their success and build strong partnerships.</p>
                <a href="{{ route('resources.best-practices') }}" class="text-blue-600 hover:text-blue-800 font-medium group-hover:underline">Learn More →</a>
            </div>
            
            <!-- Safety Guidelines -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow group">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Safety Guidelines</h3>
                <p class="text-gray-600 mb-4">Important safety and security measures to protect yourself and maintain professional standards.</p>
                <a href="{{ route('resources.safety-guidelines') }}" class="text-blue-600 hover:text-blue-800 font-medium group-hover:underline">View Guidelines →</a>
            </div>
            
            <!-- FAQ -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow group">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">FAQ</h3>
                <p class="text-gray-600 mb-4">Frequently asked questions about using the platform, payments, and common issues.</p>
                <a href="{{ route('resources.faq') }}" class="text-blue-600 hover:text-blue-800 font-medium group-hover:underline">Browse FAQ →</a>
            </div>
            
            <!-- Video Tutorials -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow group">
                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 00.707-.293l2.414-2.414a1 1 0 011.414 0l2.414 2.414a1 1 0 00.707.293H19M9 10v4a2 2 0 002 2h2a2 2 0 002-2v-4M9 10V6a2 2 0 012-2h2a2 2 0 012 2v4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Video Tutorials</h3>
                <p class="text-gray-600 mb-4">Watch comprehensive video guides covering all aspects of the platform functionality.</p>
                <a href="{{ route('resources.video-tutorials') }}" class="text-blue-600 hover:text-blue-800 font-medium group-hover:underline">Coming Soon</a>
            </div>
            
            <!-- Support Center -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow group">
                <div class="w-12 h-12 bg-gradient-to-r from-teal-500 to-green-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Support Center</h3>
                <p class="text-gray-600 mb-4">Get help from our dedicated support team available 24/7 to assist with any issues.</p>
                <a href="{{ route('resources.support') }}" class="text-blue-600 hover:text-blue-800 font-medium group-hover:underline">Get Support →</a>
            </div>
        </div>
    </div>
</div>


<!-- CTA Section -->
<div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-6">Scale Your Business</h2>
        <p class="text-xl mb-8 max-w-3xl mx-auto text-blue-100">
            Connect with verified professionals, streamline your operations, and maximize your revenue potential through OnlyVerified.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @if(Auth::check())
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-yellow-400 text-blue-900 rounded-lg font-semibold hover:bg-yellow-300 transition-colors duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Join OnlyVerified
                </a>
                <a href="{{ route('marketplace.jobs.index') }}" class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Browse Jobs
                </a>
            @endif
        </div>
    </div>
</div>
</x-layouts.marketing>

