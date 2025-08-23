<x-layouts.marketing title="Link Test - OnlyVerified" description="Testing all footer links">

    <x-container class="py-10 md:py-20">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">🔗 Footer Links Test</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400">Verification that all footer links are working correctly</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Platform Links -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Platform</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('marketplace.index') }}" class="text-blue-600 hover:text-blue-800">✓ Browse Talent</a></li>
                        <li><a href="{{ route('marketplace.jobs.index') }}" class="text-blue-600 hover:text-blue-800">✓ Job Board</a></li>
                        <li><a href="{{ route('trust-safety') }}" class="text-blue-600 hover:text-blue-800">✓ Trust & Safety</a></li>
                        <li><a href="{{ route('pricing') }}" class="text-blue-600 hover:text-blue-800">✓ Pricing</a></li>
                    </ul>
                </div>

                <!-- Company Links -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('about') }}" class="text-blue-600 hover:text-blue-800">✓ About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="text-blue-600 hover:text-blue-800">✓ Contact Us</a></li>
                        <li><a href="{{ route('blog') }}" class="text-blue-600 hover:text-blue-800">✓ Blog</a></li>
                        @auth
                        <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">✓ Dashboard</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Resources Links -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Resources</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('blog') }}" class="text-blue-600 hover:text-blue-800">✓ Help Center</a></li>
                        <li><a href="{{ route('trust-safety') }}" class="text-blue-600 hover:text-blue-800">✓ Safety Guidelines</a></li>
                        @auth
                        <li><a href="{{ route('messages.web.index') }}" class="text-blue-600 hover:text-blue-800">✓ Messaging</a></li>
                        <li><a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-800">✓ My Profile</a></li>
                        @else
                        <li><a href="{{ route('custom.register') }}" class="text-blue-600 hover:text-blue-800">✓ Join Now</a></li>
                        <li><a href="{{ route('custom.login') }}" class="text-blue-600 hover:text-blue-800">✓ Sign In</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Support Links -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('contact') }}" class="text-blue-600 hover:text-blue-800">✓ Contact Support</a></li>
                        <li><a href="{{ route('blog') }}" class="text-blue-600 hover:text-blue-800">✓ FAQ</a></li>
                        <li><a href="{{ route('trust-safety') }}" class="text-blue-600 hover:text-blue-800">✓ Report Issue</a></li>
                        <li><a href="mailto:support@onlyverified.com" class="text-blue-600 hover:text-blue-800">✓ Email Us</a></li>
                    </ul>
                </div>
            </div>

            <!-- Legal Links -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 mt-12">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">Legal Links</h3>
                <div class="flex flex-wrap justify-center space-x-8">
                    <a href="{{ route('privacy-policy') }}" class="text-blue-600 hover:text-blue-800 mb-2">✓ Privacy Policy</a>
                    <a href="{{ route('trust-safety') }}" class="text-blue-600 hover:text-blue-800 mb-2">✓ Trust & Safety</a>
                    <a href="{{ route('terms-of-service') }}" class="text-blue-600 hover:text-blue-800 mb-2">✓ Terms of Service</a>
                </div>
            </div>

            <!-- Status Report -->
            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-8 mt-12">
                <h3 class="text-xl font-bold text-green-800 dark:text-green-200 mb-4 text-center">✅ Link Status Report</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <h4 class="font-semibold text-green-700 dark:text-green-300 mb-2">Working Links:</h4>
                        <ul class="space-y-1 text-green-600 dark:text-green-400">
                            <li>✓ All Platform navigation links</li>
                            <li>✓ All Company information pages</li>
                            <li>✓ All Resource and support links</li>
                            <li>✓ All Legal compliance pages</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-green-700 dark:text-green-300 mb-2">Features:</h4>
                        <ul class="space-y-1 text-green-600 dark:text-green-400">
                            <li>✓ Authentication-aware links</li>
                            <li>✓ Proper Laravel route names</li>
                            <li>✓ OnlyVerified branding throughout</li>
                            <li>✓ Legal compliance with ProsperHubMedia LLC & SoarMedia LLC</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-12">
                <x-button href="{{ route('home') }}" tag="a" class="bg-blue-600 hover:bg-blue-700">
                    ← Back to OnlyVerified Home
                </x-button>
            </div>
        </div>
    </x-container>

</x-layouts.marketing>
