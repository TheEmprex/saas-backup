<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('settings.billing');
?>

<x-layouts.app>
    <div class="relative">
        <x-app.settings-layout
            title="Billing"
            description="Manage your billing information and payment methods"
        >
            <div class="space-y-6">
                <!-- Billing Information -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing Information</h3>
                    
                    @role('admin')
                        <x-app.alert id="admin_billing" :dismissable="false" type="info">
                            You are logged in as an admin and have full access. Billing information is managed through your admin dashboard.
                        </x-app.alert>
                    @else
                        @subscriber
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center">
                                        <x-phosphor-check-circle-duotone class="w-5 h-5 text-green-600 mr-2" />
                                        <span class="text-green-800">Active subscription: {{ auth()->user()->plan()->name }}</span>
                                    </div>
                                    <span class="text-sm text-green-600">{{ auth()->user()->planInterval() }}</span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <h4 class="font-medium text-gray-900 mb-2">Next Billing Date</h4>
                                        <p class="text-sm text-gray-600">
                                            {{ auth()->user()->subscription('default')?->asStripeSubscription()->current_period_end ? 
                                               \Carbon\Carbon::createFromTimestamp(auth()->user()->subscription('default')->asStripeSubscription()->current_period_end)->format('M d, Y') : 
                                               'Not available' }}
                                        </p>
                                    </div>
                                    
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <h4 class="font-medium text-gray-900 mb-2">Payment Method</h4>
                                        <p class="text-sm text-gray-600">
                                            {{ config('wave.billing_provider') === 'stripe' ? 'Stripe' : 'PayPal' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <x-app.alert id="no_subscription" :dismissable="false" type="warning">
                                <div class="flex items-center">
                                    <x-phosphor-warning-duotone class="w-5 h-5 text-yellow-600 mr-2" />
                                    <span>No active subscription found. Please subscribe to a plan to manage billing.</span>
                                </div>
                            </x-app.alert>
                        @endsubscriber
                    @endrole
                </div>

                <!-- Payment Methods -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Methods</h3>
                    
                    @subscriber
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600">
                                Payment methods are managed through {{ ucfirst(config('wave.billing_provider')) }}.
                            </p>
                            
                            <div class="flex space-x-3">
                                <a href="{{ route('settings.subscription') }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Manage Subscription
                                </a>
                                
                                @if(config('wave.billing_provider') === 'stripe')
                                    <a href="{{ route('settings.invoices') }}" 
                                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                        View Invoices
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-600">
                            No payment methods available. Subscribe to a plan to add payment methods.
                        </p>
                        
                        <div class="mt-4">
                            <a href="{{ route('settings.subscription') }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Subscribe Now
                            </a>
                        </div>
                    @endsubscriber
                </div>

                <!-- Billing History -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing History</h3>
                    
                    @subscriber
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600">
                                View your complete billing history and download invoices.
                            </p>
                            
                            <a href="{{ route('settings.invoices') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                                <x-phosphor-file-text-duotone class="w-4 h-4 mr-2" />
                                View All Invoices
                            </a>
                        </div>
                    @else
                        <p class="text-sm text-gray-600">
                            No billing history available. Your billing history will appear here after subscribing.
                        </p>
                    @endsubscriber
                </div>
            </div>
        </x-app.settings-layout>
    </div>
</x-layouts.app>
