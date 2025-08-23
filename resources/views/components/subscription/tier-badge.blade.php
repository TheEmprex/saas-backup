@props([
    'tier' => 'free',
    'name' => null,
    'size' => 'medium',
    'showIcon' => true
])

@php
    $tierName = $name ?? ucfirst($tier);
    $sizeClasses = [
        'small' => 'px-2 py-1 text-xs',
        'medium' => 'px-3 py-1 text-sm',
        'large' => 'px-4 py-2 text-base'
    ][$size] ?? 'px-3 py-1 text-sm';
    
    $tierClasses = match($tier) {
        'enterprise' => 'bg-purple-100 text-purple-800 border-purple-200',
        'premium' => 'bg-green-100 text-green-800 border-green-200',
        'basic' => 'bg-blue-100 text-blue-800 border-blue-200',
        'free' => 'bg-gray-100 text-gray-800 border-gray-200',
        default => 'bg-gray-100 text-gray-800 border-gray-200'
    };
    
    $tierIcons = [
        'enterprise' => 'M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z',
        'premium' => 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z',
        'basic' => 'M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z',
        'free' => 'M10 2L3 14h14l-7-12z',
    ];
@endphp

<span class="inline-flex items-center font-medium border rounded-full {{ $sizeClasses }} {{ $tierClasses }}">
    @if($showIcon && isset($tierIcons[$tier]))
        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path d="{{ $tierIcons[$tier] }}"></path>
        </svg>
    @endif
    {{ $tierName }}
    
    @if($tier === 'free')
        <span class="ml-1 text-xs opacity-75">(Free)</span>
    @endif
</span>
