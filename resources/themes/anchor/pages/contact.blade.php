<x-layouts.marketing title="Contact Us - OnlyVerified" description="Get in touch with OnlyVerified support team">

    <x-container class="py-10 md:py-20">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">Contact Us</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400">We're here to help. Get in touch with our support team.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Send us a message</h2>
                    
                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                                <input type="text" id="first_name" name="first_name" required 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                                <input type="text" id="last_name" name="last_name" required 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" required 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                            <select id="subject" name="subject" required 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Technical Support</option>
                                <option value="billing">Billing & Payments</option>
                                <option value="verification">Account Verification</option>
                                <option value="safety">Trust & Safety</option>
                                <option value="partnership">Partnership Opportunities</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                            <textarea id="message" name="message" rows="6" required 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Please describe your inquiry in detail..."></textarea>
                        </div>
                        
                        <x-button type="submit" class="w-full">Send Message</x-button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="space-y-8">
                    <!-- Support Options -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Support Options</h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Email Support</h4>
                                    <p class="text-gray-600 dark:text-gray-400 mt-1">Get help via email</p>
                                    <p class="text-blue-600 dark:text-blue-400 mt-2">support@onlyverified.com</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Response within 24 hours</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Live Chat</h4>
                                    <p class="text-gray-600 dark:text-gray-400 mt-1">Chat with our support team</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Available Monday - Friday, 9 AM - 6 PM EST</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Help Center</h4>
                                    <p class="text-gray-600 dark:text-gray-400 mt-1">Browse FAQs and guides</p>
                                    <a href="{{ route('blog') }}" class="text-blue-600 dark:text-blue-400 mt-2 inline-block">Visit Help Center →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-8">
                        <h3 class="text-xl font-bold text-red-800 dark:text-red-200 mb-4">Emergency Support</h3>
                        <p class="text-red-700 dark:text-red-300 mb-4">For urgent safety concerns or critical platform issues:</p>
                        <div class="space-y-2">
                            <p class="text-red-800 dark:text-red-200 font-semibold">Emergency Line: Available 24/7</p>
                            <p class="text-sm text-red-600 dark:text-red-400">For immediate safety threats, contact local emergency services first.</p>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Company Information</h3>
                        <div class="space-y-3 text-gray-600 dark:text-gray-400">
                            <p><strong>OnlyVerified</strong></p>
                            <p>Operated by ProsperHubMedia LLC & SoarMedia LLC</p>
                            <p class="text-sm">A trusted marketplace for verified talent</p>
                        </div>
                    </div>

                    <!-- Response Times -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-8">
                        <h3 class="text-xl font-bold text-blue-800 dark:text-blue-200 mb-4">Response Times</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-300">General Inquiries</span>
                                <span class="text-blue-800 dark:text-blue-200 font-medium">24-48 hours</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-300">Technical Support</span>
                                <span class="text-blue-800 dark:text-blue-200 font-medium">4-8 hours</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-300">Safety Concerns</span>
                                <span class="text-blue-800 dark:text-blue-200 font-medium">2-4 hours</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-300">Emergency Issues</span>
                                <span class="text-blue-800 dark:text-blue-200 font-medium">Immediate</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-container>

</x-layouts.marketing>
