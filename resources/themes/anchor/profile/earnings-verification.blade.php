<x-layouts.marketing>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Earnings Verification</h1>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if($user->earningsVerification)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Verification Status: 
                        <span class="@if($user->earningsVerification->status === 'approved') text-green-600 @elseif($user->earningsVerification->status === 'pending') text-yellow-600 @else text-red-600 @endif">
                            {{ ucfirst($user->earningsVerification->status) }}
                        </span>
                    </h2>
                    <p class="text-gray-600">Submitted on: {{ $user->earningsVerification->created_at->format('M d, Y') }}</p>
                    
                    @if($user->earningsVerification->status === 'rejected')
                        <p class="text-red-600 mt-2">Reason: {{ $user->earningsVerification->rejection_reason ?? 'No reason provided' }}</p>
                    @endif
                </div>
            @else
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Submit Earnings Verification</h2>
                    <p class="text-gray-600 mb-6">Please provide details about your earnings from other platforms to verify your capacity.</p>
                    
                    <form action="{{ route('profile.earnings-verification.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="platform_name" class="block text-sm font-medium text-gray-700 mb-2">Platform Name</label>
                                <input type="text" name="platform_name" id="platform_name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="e.g., OnlyFans, Fansly, etc." value="{{ old('platform_name') }}">
                            </div>
                            <div>
                                <label for="platform_username" class="block text-sm font-medium text-gray-700 mb-2">Username on Platform</label>
                                <input type="text" name="platform_username" id="platform_username" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Your username" value="{{ old('platform_username') }}">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="monthly_earnings" class="block text-sm font-medium text-gray-700 mb-2">Monthly Earnings ($)</label>
                            <input type="number" name="monthly_earnings" id="monthly_earnings" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0" step="0.01" placeholder="0.00" value="{{ old('monthly_earnings') }}">
                        </div>
                        
                        <div class="mb-4">
                            <label for="earnings_screenshot" class="block text-sm font-medium text-gray-700 mb-2">Earnings Screenshot *</label>
                            <input type="file" name="earnings_screenshot" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" id="earnings_screenshot" required accept=".jpg,.jpeg,.png,.gif">
                            <p class="mt-1 text-sm text-gray-500">Screenshot of your earnings dashboard (JPG, PNG, GIF - max 5MB)</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="profile_screenshot" class="block text-sm font-medium text-gray-700 mb-2">Profile Screenshot (Optional)</label>
                            <input type="file" name="profile_screenshot" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" id="profile_screenshot" accept=".jpg,.jpeg,.png,.gif">
                            <p class="mt-1 text-sm text-gray-500">Screenshot of your profile on the platform (JPG, PNG, GIF - max 5MB)</p>
                        </div>
                        
                        <div class="mb-6">
                            <label for="additional_notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                            <textarea name="additional_notes" id="additional_notes" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Any additional information that might help with verification">{{ old('additional_notes') }}</textarea>
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">Submit Verification</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-layouts.marketing>
