<header 
    x-data="{ 
        mobileMenuOpen: false, 
        scrolled: false, 
        showOverlay: false,
        topOffset: '10',
        evaluateScrollPosition(){
            if(window.pageYOffset > this.topOffset){
                this.scrolled = true;
            } else {
                this.scrolled = false;
            }
        } 
    }"
    x-init="
        window.addEventListener('resize', function() {
            if(window.innerWidth > 768) {
                mobileMenuOpen = false;
            }
        });
        $watch('mobileMenuOpen', function(value){
            if(value){ document.body.classList.add('overflow-hidden'); } else { document.body.classList.remove('overflow-hidden'); }
        });
        evaluateScrollPosition();
        window.addEventListener('scroll', function() {
            evaluateScrollPosition(); 
        })
    " 
    class="sticky top-0 z-50 w-full bg-white border-b border-gray-200 shadow-sm"
>
    <div 
        x-show="showOverlay"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="absolute inset-0 w-full h-screen pt-24" x-cloak>
        <div class="w-screen h-full bg-black/50"></div>
    </div>
    <x-container>
        <div class="z-30 flex items-center justify-between h-24 md:space-x-8">
            <div class="z-20 flex items-center justify-between w-full md:w-auto">
                <div class="relative z-20 inline-flex">
                    <a href="{{ route('home') }}" class="flex items-center justify-center space-x-3 font-bold text-zinc-900 dark:text-zinc-100">
                    <x-logo class="w-auto h-8 md:h-9"></x-logo>
                    </a>
                </div>
                <div class="flex justify-end flex-grow md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 transition duration-150 ease-in-out rounded-full text-zinc-400 hover:text-zinc-500 hover:bg-zinc-100">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-8">
                <!-- Platform Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="flex items-center space-x-1 px-3 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors duration-200">
                        <span>Platform</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 transform scale-95" 
                         x-transition:enter-end="opacity-100 transform scale-100" 
                         x-transition:leave="transition ease-in duration-150" 
                         x-transition:leave-start="opacity-100 transform scale-100" 
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute top-full left-0 mt-2 w-96 bg-white rounded-xl border border-gray-200 shadow-xl z-[9999]"
                         style="min-width: 320px; max-width: 400px;">
                        <div class="p-4 space-y-2">
                            <a href="{{ route('marketplace.index') }}" class="flex items-start space-x-3 p-3 hover:bg-gray-50 transition-colors duration-150 rounded-lg">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900">Browse Talent</h3>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Find verified professionals</p>
                                </div>
                            </a>
                            <a href="{{ route('marketplace.jobs.index') }}" class="flex items-start space-x-3 p-3 hover:bg-gray-50 transition-colors duration-150 rounded-lg">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900">Job Board</h3>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Post or find opportunities</p>
                                </div>
                            </a>
                            <a href="{{ route('messages.index') }}" class="flex items-start space-x-3 p-3 hover:bg-gray-50 transition-colors duration-150 rounded-lg">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900">Messaging</h3>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Communicate securely</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Resources Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="flex items-center space-x-1 px-3 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors duration-200">
                        <span>Resources</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 transform scale-95" 
                         x-transition:enter-end="opacity-100 transform scale-100" 
                         x-transition:leave="transition ease-in duration-150" 
                         x-transition:leave-start="opacity-100 transform scale-100" 
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute top-full left-0 mt-2 w-64 bg-white rounded-xl border border-gray-200 py-3 z-[9999]"
                         style="box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); transform-origin: top left;">
                        <div class="grid grid-cols-1 gap-2">
                            <a href="{{ route('terms-of-service') }}" class="px-4 py-3 hover:bg-gray-50 transition-colors duration-150 rounded-lg mx-2">
                                <h3 class="text-sm font-medium text-gray-900">Terms of Service</h3>
                                <p class="text-xs text-gray-500 mt-1">Our terms and conditions</p>
                            </a>
                            <a href="{{ route('privacy-policy') }}" class="px-4 py-3 hover:bg-gray-50 transition-colors duration-150 rounded-lg mx-2">
                                <h3 class="text-sm font-medium text-gray-900">Privacy Policy</h3>
                                <p class="text-xs text-gray-500 mt-1">How we protect your data</p>
                            </a>
                            <a href="{{ route('trust-safety') }}" class="px-4 py-3 hover:bg-gray-50 transition-colors duration-150 rounded-lg mx-2">
                                <h3 class="text-sm font-medium text-gray-900">Trust & Safety</h3>
                                <p class="text-xs text-gray-500 mt-1">Platform safety commitment</p>
                            </a>
                            <a href="{{ route('contact') }}" class="px-4 py-3 hover:bg-gray-50 transition-colors duration-150 rounded-lg mx-2">
                                <h3 class="text-sm font-medium text-gray-900">Contact Support</h3>
                                <p class="text-xs text-gray-500 mt-1">Get help from our team</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Simple Links -->
                <a href="{{ route('pricing') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors duration-200">Pricing</a>
                <a href="{{ route('blog') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors duration-200">Blog</a>
            </nav>

            <!-- Mobile Navigation -->
            <nav :class="{ 'hidden': !mobileMenuOpen }" class="md:hidden absolute top-full left-0 right-0 bg-white border-t border-gray-200 shadow-lg">
                <div class="px-4 py-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Platform</h3>
                        <div class="pl-4 space-y-3">
                            <a href="{{ route('marketplace.index') }}" class="block text-sm text-gray-600 hover:text-indigo-600">Browse Talent</a>
                            <a href="{{ route('marketplace.jobs.index') }}" class="block text-sm text-gray-600 hover:text-indigo-600">Job Board</a>
                            <a href="{{ route('messages.index') }}" class="block text-sm text-gray-600 hover:text-indigo-600">Messaging</a>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Resources</h3>
                        <div class="pl-4 space-y-3">
                            <a href="{{ route('terms-of-service') }}" class="block text-sm text-gray-600 hover:text-indigo-600">Terms of Service</a>
                            <a href="{{ route('privacy-policy') }}" class="block text-sm text-gray-600 hover:text-indigo-600">Privacy Policy</a>
                            <a href="{{ route('contact') }}" class="block text-sm text-gray-600 hover:text-indigo-600">Contact Support</a>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <a href="{{ route('pricing') }}" class="block text-sm font-medium text-gray-900 mb-3">Pricing</a>
                        <a href="{{ route('blog') }}" class="block text-sm font-medium text-gray-900 mb-4">Blog</a>
                        
                        @guest
                            <div class="space-y-3 pt-4">
                                <x-button href="{{ route('login') }}" tag="a" class="w-full text-sm" color="secondary">Login</x-button>
                                <x-button href="{{ route('register') }}" tag="a" class="w-full text-sm">Sign Up</x-button>
                            </div>
                        @else
                            <div class="pt-4">
                                <x-button href="{{ route('dashboard') }}" tag="a" class="w-full text-sm">View Dashboard</x-button>
                            </div>
                        @endguest
                    </div>
                </div>
            </nav>
            
            @guest
                <div class="relative z-30 items-center justify-center flex-shrink-0 hidden h-full space-x-3 text-sm md:flex">
                    <x-button href="{{ route('login') }}" tag="a" class="text-sm" color="secondary">Login</x-button>
                    <x-button href="{{ route('register') }}" tag="a" class="text-sm">Sign Up</x-button>
                </div>
            @else
                <x-button href="{{ route('dashboard') }}" tag="a" class="text-sm" class="relative z-20 flex-shrink-0 hidden ml-2 md:block">View Dashboard</x-button>
            @endguest

        </div>
    </x-container>

</header>
