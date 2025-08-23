@props([
    'feature' => null,
    'tier' => null,
    'message' => null,
    'size' => 'medium'
])

@php
    $defaultMessage = $message ?? 'Upgrade your subscription to unlock this feature';
    $sizeClasses = [
        'small' => 'p-3 text-sm',
        'medium' => 'p-4',
        'large' => 'p-6 text-lg'
    ][$size] ?? 'p-4';
@endphp

<div class="bg-gradient-to-r from-blue-50 to-indigo-100 border border-blue-200 rounded-lg {{ $sizeClasses }}">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
        </div>
        
        <div class="flex-1 min-w-0">
            <h4 class="text-sm font-semibold text-gray-800">Upgrade Required</h4>
            <p class="mt-1 text-sm text-gray-600">{{ $defaultMessage }}</p>
            
            @if($feature || $tier)
                <div class="mt-2 text-xs text-gray-500">
                    @if($feature)
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst(str_replace('_', ' ', $feature)) }}
                        </span>
                    @endif
                    @if($tier)
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-purple-100 text-purple-800 ml-1">
                            {{ ucfirst($tier) }} Plan
                        </span>
                    @endif
                </div>
            @endif
        </div>
        
        <div class="flex-shrink-0">
            <a href="{{ route('subscription.plans') }}" 
               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                </svg>
                Upgrade
            </a>
        </div>
    </div>
</div>
