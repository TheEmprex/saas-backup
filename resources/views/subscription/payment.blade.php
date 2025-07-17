<x-layouts.marketing>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-8 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                    <h1 class="text-3xl font-bold mb-2">Complete Your Payment</h1>
                    @if(session('upgrade_info'))
                        <p class="text-blue-100">Upgrading to {{ $plan->name }} - ${{ $plan->price }}/month</p>
                    @elseif(session('downgrade_info'))
                        <p class="text-blue-100">Downgrading to {{ $plan->name }} - ${{ $plan->price }}/month</p>
                    @else
                        <p class="text-blue-100">You're subscribing to {{ $plan->name }} - ${{ $plan->price }}/month</p>
                    @endif
                </div>
                
                @if(session('upgrade_info') || session('downgrade_info'))
                    <!-- Plan Change Summary -->
                    <div class="px-6 py-4 bg-blue-50 border-b">
                        @php
                            $changeInfo = session('upgrade_info') ?? session('downgrade_info');
                        @endphp
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-medium text-blue-900">{{ ucfirst($changeInfo['type']) }} Summary</h3>
                                <p class="text-sm text-blue-700">{{ $changeInfo['message'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-blue-700">Net Amount:</p>
                                <p class="text-lg font-bold text-blue-900">${{ number_format($changeInfo['net_amount'], 2) }}</p>
                            </div>
                        </div>
                        @if(isset($changeInfo['warnings']) && count($changeInfo['warnings']) > 0)
                            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                <h4 class="font-medium text-yellow-800 mb-1">Warnings:</h4>
                                <ul class="text-sm text-yellow-700 space-y-1">
                                    @foreach($changeInfo['warnings'] as $warning)
                                        <li>• {{ $warning }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">
                    <!-- Payment Methods -->
                    <div>
                        <h2 class="text-xl font-semibold mb-6">Choose Payment Method</h2>
                        
                        <!-- Payment Method Tabs -->
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="-mb-px flex space-x-8">
                                <button id="card-tab" class="payment-tab active py-2 px-1 border-b-2 font-medium text-sm" data-target="card-payment">
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Credit Card
                                </button>
                                <button id="crypto-tab" class="payment-tab py-2 px-1 border-b-2 font-medium text-sm" data-target="crypto-payment">
                                    <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8.070 8.340 8.433 7.418zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.364.243 0 .697-.155.103-.346.196-.567.267z"></path>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 5a1 1 0 012 0v.092a4.535 4.535 0 011.676.662C13.398 6.28 14 7.36 14 8.5c0 1.441-.793 2.307-1.676 2.746A4.535 4.535 0 0111 11.908V12a1 1 0 11-2 0v-.092a4.535 4.535 0 01-1.676-.662C6.602 10.72 6 9.64 6 8.5c0-1.441.793-2.307 1.676-2.746A4.535 4.535 0 019 5.092V5z" clip-rule="evenodd"></path>
                                    </svg>
                                    Cryptocurrency
                                </button>
                            </nav>
                        </div>

                        <!-- Credit Card Payment -->
                        <div id="card-payment" class="payment-content">
                            <form method="POST" action="{{ route('subscription.payment.success') }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <input type="hidden" name="payment_method" value="card">
                                <input type="hidden" name="transaction_id" value="{{ Str::random(20) }}">
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                        <input type="text" name="card_number" placeholder="1234 5678 9012 3456" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                            <input type="text" name="expiry_date" placeholder="MM/YY" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                            <input type="text" name="cvv" placeholder="123" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                                        <input type="text" name="cardholder_name" placeholder="John Doe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-md font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200">
                                        Pay ${{ $plan->price }} with Card
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Crypto Payment -->
                        <div id="crypto-payment" class="payment-content hidden">
                            <form id="crypto-form" method="POST" action="{{ route('subscription.payment.success') }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <input type="hidden" name="payment_method" value="crypto">
                                <input type="hidden" name="transaction_id" value="{{ Str::random(20) }}">
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Cryptocurrency</label>
                                        <select name="crypto_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="BTC">Bitcoin (BTC)</option>
                                            <option value="ETH">Ethereum (ETH)</option>
                                            <option value="USDT">Tether (USDT)</option>
                                            <option value="USDC">USD Coin (USDC)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-md">
                                        <h3 class="font-medium mb-2">Payment Instructions</h3>
                                        <p class="text-sm text-gray-600 mb-3">Send exactly <strong>${{ $plan->price }}</strong> worth of cryptocurrency to the address below:</p>
                                        
                                        <div class="bg-white p-3 rounded border">
                                            <div class="flex items-center justify-between">
                                                <code id="wallet-address" class="text-sm font-mono text-gray-800">1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2</code>
                                                <button type="button" id="copy-button" class="text-blue-500 hover:text-blue-600 text-sm">Copy</button>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 text-center">
                                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRkZGRkZGIi8+CjxyZWN0IHg9IjIwIiB5PSIyMCIgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiBmaWxsPSIjMDAwMDAwIi8+CjxyZWN0IHg9IjMwIiB5PSIyMCIgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiBmaWxsPSIjMDAwMDAwIi8+CjxyZWN0IHg9IjUwIiB5PSIyMCIgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiBmaWxsPSIjMDAwMDAwIi8+CjxyZWN0IHg9IjYwIiB5PSIyMCIgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiBmaWxsPSIjMDAwMDAwIi8+CjxyZWN0IHg9IjcwIiB5PSIyMCIgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiBmaWxsPSIjMDAwMDAwIi8+Cjwvc3ZnPg==" alt="Payment QR Code" class="w-32 h-32 mx-auto border rounded">
                                            <p class="text-xs text-gray-500 mt-2">QR Code for easy payment</p>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                        <h4 class="font-medium text-yellow-800 mb-1">Important Notes:</h4>
                                        <ul class="text-sm text-yellow-700 space-y-1">
                                            <li>• Payment confirmation may take 10-30 minutes</li>
                                            <li>• Only send the exact amount to avoid delays</li>
                                            <li>• Double-check the wallet address before sending</li>
                                        </ul>
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-yellow-500 text-white py-3 px-4 rounded-md font-medium hover:from-orange-600 hover:to-yellow-600 transition-all duration-200">
                                        I've Sent the Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div>
                        <h2 class="text-xl font-semibold mb-6">Order Summary</h2>
                        
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <span class="font-medium">{{ $plan->name }}</span>
                                <span class="font-bold">${{ $plan->price }}</span>
                            </div>
                            
                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span>Subtotal</span>
                                    <span>${{ $plan->price }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span>Tax</span>
                                    <span>$0.00</span>
                                </div>
                                <div class="flex justify-between items-center font-bold text-lg pt-2 border-t">
                                    <span>Total</span>
                                    <span>${{ $plan->price }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 space-y-4">
                            <h3 class="font-medium">What you'll get:</h3>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Job Posts: {{ $plan->job_post_limit ?? 'Unlimited' }}
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Applications: {{ $plan->chat_application_limit ?? 'Unlimited' }}
                                </li>
                                @if ($plan->unlimited_chats)
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Unlimited Chats
                                </li>
                                @endif
                                @if ($plan->advanced_filters)
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Advanced Filters
                                </li>
                                @endif
                                @if ($plan->analytics)
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Analytics Dashboard
                                </li>
                                @endif
                            </ul>
                        </div>
                        
                        <div class="mt-6 text-xs text-gray-500">
                            <p>By completing this purchase, you agree to our Terms of Service and Privacy Policy.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.payment-tab');
            const contents = document.querySelectorAll('.payment-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all content divs
                    contents.forEach(content => content.classList.add('hidden'));
                    // Show target content
                    const target = document.getElementById(this.getAttribute('data-target'));
                    if (target) {
                        target.classList.remove('hidden');
                    }
                });
            });
            
            // Copy wallet address functionality
            const copyButton = document.getElementById('copy-button');
            const walletAddress = document.getElementById('wallet-address');
            
            if (copyButton && walletAddress) {
                copyButton.addEventListener('click', async function() {
                    try {
                        await navigator.clipboard.writeText(walletAddress.textContent);
                        
                        // Show success feedback
                        const originalText = copyButton.textContent;
                        copyButton.textContent = 'Copied!';
                        copyButton.classList.add('text-green-500');
                        copyButton.classList.remove('text-blue-500');
                        
                        // Reset after 2 seconds
                        setTimeout(() => {
                            copyButton.textContent = originalText;
                            copyButton.classList.remove('text-green-500');
                            copyButton.classList.add('text-blue-500');
                        }, 2000);
                    } catch (err) {
                        console.error('Failed to copy: ', err);
                        // Fallback for older browsers
                        const textArea = document.createElement('textarea');
                        textArea.value = walletAddress.textContent;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                        
                        copyButton.textContent = 'Copied!';
                        setTimeout(() => {
                            copyButton.textContent = 'Copy';
                        }, 2000);
                    }
                });
            }
        });
    </script>

    <style>
        .payment-tab {
            @apply text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300;
        }
        .payment-tab.active {
            @apply text-blue-600 border-blue-600;
        }
    </style>
</x-layouts.marketing>
