@props(['title' => 'Subscription Status'])

<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $title }}</h3>
            
            @auth
                <!-- Subscription Tier Badge -->
                <x-subscription.tier-badge 
                    :tier="$userSubscription['tier']['tier']" 
                    :name="$userSubscription['tier']['name']" 
                />
            @endauth
        </div>
        
        @auth
            <!-- Subscription Details -->
            <div class="mt-4">
                @hasSubscription
                    <!-- Active Subscription -->
                    <div class="text-sm text-gray-600">
                        <p>Current Plan: <span class="font-medium">{{ $userSubscription['tier']['name'] }}</span></p>
                        
                        @if($userSubscription['tier']['expires_at'])
                            <p>Expires: <span class="font-medium">{{ $userSubscription['tier']['expires_at']->format('M j, Y') }}</span></p>
                        @else
                            <p><span class="text-green-600 font-medium">Active</span></p>
                        @endif
                    </div>
                    
                    @subscriptionExpiresSoon
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                            <p class="text-sm text-yellow-800">
                                ⚠️ Your subscription expires soon. 
                                <a href="{{ route('subscription.plans') }}" class="font-medium underline">Renew now</a>
                            </p>
                        </div>
                    @endif
                @else
                    <!-- Free Plan -->
                    <x-subscription.upgrade-prompt 
                        message="You're currently on the free plan. Upgrade to unlock premium features!"
                        size="small"
                    />
                @endhasSubscription
                
                <!-- Usage Statistics for Subscribed Users -->
                @hasSubscription
                    <div class="mt-4 grid grid-cols-1 gap-3">
                        @userType('agency')
                            <!-- Job Posts Usage -->
                            <x-subscription.usage-indicator 
                                type="job_posts"
                                :used="$userLimits['job_posts']['used_this_month']"
                                :limit="$userLimits['job_posts']['limit'] ?? $userSubscription['tier']['limits']['job_posts']"
                                :remaining="$userLimits['job_posts']['remaining']"
                                :showDetails="false"
                            />
                        @endif
                        
                        @userType('chatter')
                            <!-- Applications Usage -->
                            <x-subscription.usage-indicator 
                                type="applications"
                                :used="$userLimits['applications']['used_this_month']"
                                :limit="$userLimits['applications']['limit'] ?? $userSubscription['tier']['limits']['applications']"
                                :remaining="$userLimits['applications']['remaining']"
                                :showDetails="false"
                            />
                        @endif
                        
                        <!-- Conversations -->
                        <x-subscription.usage-indicator 
                            type="conversations"
                            :used="$userLimits['conversations']['count']"
                            :limit="$userSubscription['tier']['limits']['conversations']"
                            :showDetails="false"
                        />
                    </div>
                @endhasSubscription
                
                <!-- Feature Preview -->
                <div class="mt-4 space-y-2">
                    <h4 class="text-sm font-medium text-gray-900">Available Features</h4>
                    
                    <div class="flex flex-wrap gap-1">
                        @hasFeature('basic_messaging')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Basic Messaging
                            </span>
                        @endif
                        
                        @hasFeature('enhanced_messaging')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Enhanced Messaging
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Enhanced Messaging
                            </span>
                        @endif
                        
                        @hasFeature('file_uploads')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ File Uploads
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                File Uploads
                            </span>
                        @endif
                        
                        @hasFeature('advanced_filters')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Advanced Filters
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Advanced Filters
                            </span>
                        @endif
                        
                        @hasFeature('analytics')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Analytics
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Analytics
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="mt-4 flex space-x-3">
                    @canPostJob
                        <a href="{{ route('job-posts.create') }}" 
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Post Job
                        </a>
                    @else
                        @hasReachedLimit('job_posts')
                            <span class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-500 bg-gray-100 cursor-not-allowed">
                                Job Limit Reached
                            </span>
                        @else
                            <a href="{{ route('subscription.plans') }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                                Subscribe to Post Jobs
                            </a>
                        @endif
                    @endcanPostJob
                    
                    <a href="{{ route('subscription.plans') }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        View Plans
                    </a>
                </div>
            </div>
        @else
            <!-- Guest User -->
            <div class="mt-4">
                <p class="text-sm text-gray-600">Sign in to see your subscription status and usage.</p>
                <div class="mt-3 flex space-x-3">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sign Up
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>
