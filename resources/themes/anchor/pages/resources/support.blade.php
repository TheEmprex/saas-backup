<x-layouts.marketing title="Support - OnlyVerified" description="Get help and support for OnlyVerified platform">

    <x-container class="py-10 md:py-20">
        <div class="max-w-4xl mx-auto">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-gray-100 mb-6">Support Center</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                    We're here to help! Get support, report issues, or find answers to your questions.
                </p>
            </div>

            <!-- Quick Support Options -->
            <div class="grid md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">Live Chat</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Get instant help from our support team</p>
                    <p class="text-sm text-green-600 dark:text-green-400 mb-4">● Available 24/7</p>
                    <x-button tag="button" color="primary" size="sm" class="w-full">
                        Start Chat
                    </x-button>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">Email Support</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Send us a detailed message</p>
                    <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">Response within 2 hours</p>
                    <x-button href="mailto:support@onlyverified.com" tag="a" color="primary" size="sm" class="w-full">
                        Send Email
                    </x-button>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">Help Center</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Browse articles and guides</p>
                    <p class="text-sm text-purple-600 dark:text-purple-400 mb-4">Self-service support</p>
                    <x-button href="{{ route('resources.faq') }}" tag="a" color="primary" size="sm" class="w-full">
                        Browse FAQ
                    </x-button>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Send us a Message</h2>
                
                <form class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                        <select id="subject" name="subject" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">Select a topic</option>
                            <option value="account">Account Issues</option>
                            <option value="payment">Payment Problems</option>
                            <option value="technical">Technical Support</option>
                            <option value="safety">Safety Concerns</option>
                            <option value="verification">Verification Issues</option>
                            <option value="general">General Inquiry</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority Level</label>
                        <select id="priority" name="priority" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="low">Low - General question</option>
                            <option value="medium">Medium - Account issue</option>
                            <option value="high">High - Urgent problem</option>
                            <option value="critical">Critical - Safety concern</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                        <textarea id="message" name="message" rows="6" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Please provide as much detail as possible about your issue or question..."></textarea>
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" id="consent" name="consent" class="mt-1 mr-2">
                        <label for="consent" class="text-sm text-gray-600 dark:text-gray-400">
                            I consent to OnlyVerified storing and processing my personal data to respond to my inquiry. View our <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Privacy Policy</a>.
                        </label>
                    </div>

                    <x-button type="submit" class="w-full">
                        Send Message
                    </x-button>
                </form>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-6 mb-12">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mr-4 mt-1">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">Emergency Support</h3>
                        <p class="text-red-700 dark:text-red-300 mb-3">
                            If you're experiencing safety concerns, harassment, or urgent security issues, contact our emergency support line immediately.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <x-button href="tel:+1-800-VERIFY" tag="a" color="danger" size="sm">
                                📞 Call Emergency Line
                            </x-button>
                            <x-button tag="button" color="danger" size="sm" class="bg-red-600 hover:bg-red-700">
                                🚨 Report Safety Issue
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Resources -->
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Popular Help Topics</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('resources.getting-started') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                How to get started on OnlyVerified
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('resources.safety-guidelines') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                Safety and security guidelines
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('resources.best-practices') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                Best practices for success
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('resources.faq') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                Payment and billing questions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('resources.faq') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                Account verification process
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Contact Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">General Support</p>
                            <p class="text-gray-600 dark:text-gray-400">support@onlyverified.com</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">Safety & Security</p>
                            <p class="text-gray-600 dark:text-gray-400">safety@onlyverified.com</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">Business Inquiries</p>
                            <p class="text-gray-600 dark:text-gray-400">business@onlyverified.com</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">Support Hours</p>
                            <p class="text-gray-600 dark:text-gray-400">24/7 Live Chat & Email</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-container>

</x-layouts.marketing>
