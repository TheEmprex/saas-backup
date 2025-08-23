<x-layouts.marketing title="Getting Started Guide - OnlyVerified" description="Complete guide to getting started on OnlyVerified">

    <x-container class="py-10 md:py-20">
        <div class="max-w-4xl mx-auto">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-gray-100 mb-6">Getting Started Guide</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                    Everything you need to know to set up your profile and start succeeding on OnlyVerified.
                </p>
            </div>

            <!-- Content -->
            <div class="prose prose-lg max-w-none dark:prose-invert">
                <h2>Welcome to OnlyVerified!</h2>
                <p>Thank you for joining OnlyVerified, the premier marketplace for verified professionals in the digital entertainment industry. This guide will walk you through everything you need to know to get started successfully.</p>

                <h3>1. Create Your Account</h3>
                <p>Begin by creating your account and selecting your role (Chatter, OFM Agency, or Chatting Agency). Make sure to choose the role that best matches your services.</p>

                <h3>2. Complete Your Profile</h3>
                <ul>
                    <li><strong>Profile Picture:</strong> Upload a professional photo</li>
                    <li><strong>Bio:</strong> Write a compelling description of your services</li>
                    <li><strong>Skills:</strong> List your relevant skills and experience</li>
                    <li><strong>Portfolio:</strong> Add samples of your work (if applicable)</li>
                </ul>

                <h3>3. Verification Process</h3>
                <p>Complete the verification process to get your verified badge:</p>
                <ul>
                    <li>Submit required identification documents</li>
                    <li>Provide proof of experience</li>
                    <li>Pass the skills assessment (for chatters)</li>
                </ul>

                <h3>4. Start Connecting</h3>
                <p>Once verified, you can start browsing opportunities, applying to jobs, and connecting with other professionals on the platform.</p>

                <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                    <h4 class="text-blue-800 dark:text-blue-200 mb-2">💡 Pro Tip</h4>
                    <p class="text-blue-700 dark:text-blue-300 mb-0">Complete your profile 100% to increase your chances of being discovered by potential clients.</p>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-8 text-white mt-16">
                <h2 class="text-2xl font-bold mb-4">Ready to Get Started?</h2>
                <p class="mb-6">Join thousands of professionals already succeeding on OnlyVerified.</p>
                <x-button href="{{ route('register') }}" tag="a" color="secondary" class="w-full sm:w-auto">
                    Create Your Account
                </x-button>
            </div>
        </div>
    </x-container>

</x-layouts.marketing>
