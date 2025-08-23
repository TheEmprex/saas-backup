<x-layouts.marketing title="Safety Guidelines - OnlyVerified" description="Important safety and security guidelines">

    <x-container class="py-10 md:py-20">
        <div class="max-w-4xl mx-auto">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-gray-100 mb-6">Safety Guidelines</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                    Important safety and security measures to protect yourself and maintain professional standards.
                </p>
            </div>

            <!-- Content -->
            <div class="prose prose-lg max-w-none dark:prose-invert">
                <h2>General Safety Guidelines</h2>
                
                <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-lg">
                    <h4 class="text-red-800 dark:text-red-200 mb-2">⚠️ Important Safety Notice</h4>
                    <p class="text-red-700 dark:text-red-300 mb-0">Always prioritize your safety and well-being when working on OnlyVerified. Report any suspicious activity immediately.</p>
                </div>

                <h3>Personal Information Protection</h3>
                <ul>
                    <li><strong>Never share personal information:</strong> Avoid sharing your real address, phone number, or financial details through messages</li>
                    <li><strong>Use platform messaging:</strong> Keep all communications within OnlyVerified's secure messaging system</li>
                    <li><strong>Verify client identity:</strong> Work only with verified employers and agencies</li>
                    <li><strong>Secure passwords:</strong> Use strong, unique passwords for your account</li>
                </ul>

                <h3>Professional Boundaries</h3>
                <ul>
                    <li><strong>Clear work scope:</strong> Always define the scope of work before starting any project</li>
                    <li><strong>Payment terms:</strong> Agree on payment terms and rates upfront</li>
                    <li><strong>Professional communication:</strong> Maintain professional boundaries in all interactions</li>
                    <li><strong>Contract agreements:</strong> Use OnlyVerified's contract system for all agreements</li>
                </ul>

                <h3>Financial Security</h3>
                <ul>
                    <li><strong>Platform payments only:</strong> Always use OnlyVerified's payment system</li>
                    <li><strong>Avoid external payments:</strong> Never accept payments outside the platform</li>
                    <li><strong>Document everything:</strong> Keep records of all work and payments</li>
                    <li><strong>Report payment issues:</strong> Contact support for any payment disputes</li>
                </ul>

                <h3>Red Flags to Watch For</h3>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg">
                    <h4 class="text-yellow-800 dark:text-yellow-200 mb-3">Warning Signs</h4>
                    <ul class="text-yellow-700 dark:text-yellow-300 mb-0">
                        <li>Requests for personal information or photos</li>
                        <li>Offers of payment outside the platform</li>
                        <li>Pressure to start work without proper agreements</li>
                        <li>Unprofessional or inappropriate communication</li>
                        <li>Requests to move conversations off-platform</li>
                    </ul>
                </div>

                <h3>Reporting and Support</h3>
                <ul>
                    <li><strong>Report violations:</strong> Use the report function for any policy violations</li>
                    <li><strong>Contact support:</strong> Reach out to our 24/7 support team for assistance</li>
                    <li><strong>Block problematic users:</strong> Use blocking features when necessary</li>
                    <li><strong>Document incidents:</strong> Keep screenshots and records of problematic interactions</li>
                </ul>

                <h3>Account Security</h3>
                <ul>
                    <li><strong>Two-factor authentication:</strong> Enable 2FA for additional security</li>
                    <li><strong>Regular password updates:</strong> Change your password regularly</li>
                    <li><strong>Monitor account activity:</strong> Review your account regularly for suspicious activity</li>
                    <li><strong>Secure devices:</strong> Only access your account from secure, trusted devices</li>
                </ul>
            </div>

            <!-- Emergency Contact -->
            <div class="text-center bg-gradient-to-r from-red-600 to-pink-600 rounded-xl p-8 text-white mt-16">
                <h2 class="text-2xl font-bold mb-4">Need Immediate Help?</h2>
                <p class="mb-6">If you feel unsafe or need immediate assistance, contact our support team immediately.</p>
                <x-button href="{{ route('resources.support') }}" tag="a" color="secondary" class="w-full sm:w-auto">
                    Contact Support
                </x-button>
            </div>
        </div>
    </x-container>

</x-layouts.marketing>
