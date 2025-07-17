<x-layouts.marketing>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Earnings Verification Details</h1>
                    <p class="text-gray-600 mt-2">Review and update verification status</p>
                </div>
                <a href="{{ route('admin.earnings.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Back to List
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Verification Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Verification Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                            <p class="text-sm text-gray-900">{{ $verification->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $verification->user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                            <p class="text-sm text-gray-900">{{ $verification->platform_name }}</p>
                            <p class="text-sm text-gray-500">@{{ $verification->platform_username }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Earnings</label>
                            <p class="text-sm text-gray-900">${{ number_format($verification->monthly_earnings, 2) }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $verification->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($verification->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($verification->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Submitted</label>
                            <p class="text-sm text-gray-900">{{ $verification->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        
                        @if($verification->verified_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Verified</label>
                                <p class="text-sm text-gray-900">{{ $verification->verified_at->format('M d, Y g:i A') }}</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($verification->additional_notes)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                            <p class="text-sm text-gray-900">{{ $verification->additional_notes }}</p>
                        </div>
                    @endif
                    
                    @if($verification->rejection_reason)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason</label>
                            <p class="text-sm text-red-600">{{ $verification->rejection_reason }}</p>
                        </div>
                    @endif
                </div>

                <!-- Screenshots -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Uploaded Files</h2>
                    
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Earnings Screenshot</h3>
                        @if($verification->earnings_screenshot_path)
                            <div class="space-y-2">
                                <img src="{{ route('admin.earnings.preview', [$verification, 'earnings_screenshot']) }}" 
                                     alt="Earnings Screenshot" 
                                     class="w-full max-w-md h-auto rounded-lg border border-gray-300 shadow-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.earnings.download', [$verification, 'earnings_screenshot']) }}" 
                                       class="inline-block bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        Download File
                                    </a>
                                    <a href="{{ route('admin.earnings.preview', [$verification, 'earnings_screenshot']) }}" 
                                       target="_blank"
                                       class="inline-block bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700">
                                        View Full Size
                                    </a>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No file uploaded</p>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Profile Screenshot</h3>
                        @if($verification->profile_screenshot_path)
                            <div class="space-y-2">
                                <img src="{{ route('admin.earnings.preview', [$verification, 'profile_screenshot']) }}" 
                                     alt="Profile Screenshot" 
                                     class="w-full max-w-md h-auto rounded-lg border border-gray-300 shadow-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.earnings.download', [$verification, 'profile_screenshot']) }}" 
                                       class="inline-block bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        Download File
                                    </a>
                                    <a href="{{ route('admin.earnings.preview', [$verification, 'profile_screenshot']) }}" 
                                       target="_blank"
                                       class="inline-block bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700">
                                        View Full Size
                                    </a>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No file uploaded</p>
                        @endif
                    </div>
                </div>
                </div>
            </div>

            <!-- Status Update -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Update Status</h2>
                    
                    <form method="POST" action="{{ route('admin.earnings.update-status', $verification) }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="pending" {{ $verification->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $verification->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $verification->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason (if rejected)</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Provide a reason for rejection">{{ $verification->rejection_reason }}</textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Update Status
                        </button>
                    </form>
                </div>
                
                <!-- User Information -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-xl font-semibold mb-4">User Information</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="text-sm text-gray-900">{{ $verification->user->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="text-sm text-gray-900">{{ $verification->user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">User Type</label>
                            <p class="text-sm text-gray-900">{{ $verification->user->userType->name ?? 'Not set' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Member Since</label>
                            <p class="text-sm text-gray-900">{{ $verification->user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.marketing>
