@props([
    'type' => 'job_posts',
    'used' => 0,
    'limit' => null,
    'remaining' => null,
    'label' => null,
    'showDetails' => true
])

@php
    $labels = [
        'job_posts' => 'Job Posts',
        'applications' => 'Applications',
        'conversations' => 'Conversations'
    ];
    
    $displayLabel = $label ?? ($labels[$type] ?? ucfirst($type));
    $isUnlimited = $limit === null || $limit === 0;
    $percentage = $isUnlimited ? 0 : (($limit > 0) ? min(100, ($used / $limit) * 100) : 100);
    
    $statusColor = match(true) {
        $percentage >= 90 => 'red',
        $percentage >= 70 => 'yellow',
        $percentage >= 50 => 'blue',
        default => 'green'
    };
    
    $statusClasses = [
        'red' => 'bg-red-100 text-red-800 border-red-200',
        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
        'green' => 'bg-green-100 text-green-800 border-green-200'
    ];
    
    $progressClasses = [
        'red' => 'bg-red-500',
        'yellow' => 'bg-yellow-500',
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500'
    ];
@endphp

<div class="border rounded-lg p-4 {{ $statusClasses[$statusColor] }}">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <h4 class="font-semibold text-sm">{{ $displayLabel }}</h4>
            
            @if($isUnlimited)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Unlimited
                </span>
            @endif
        </div>
        
        <div class="text-sm font-medium">
            @if($isUnlimited)
                {{ $used }}
            @else
                {{ $used }} / {{ $limit }}
            @endif
        </div>
    </div>
    
    @if(!$isUnlimited && $showDetails)
        <!-- Progress Bar -->
        <div class="mt-3">
            <div class="flex justify-between text-xs text-gray-600 mb-1">
                <span>{{ $used }} used</span>
                @if($remaining !== null)
                    <span>{{ $remaining }} remaining</span>
                @endif
            </div>
            <div class="w-full bg-white rounded-full h-2 shadow-inner">
                <div class="h-2 rounded-full {{ $progressClasses[$statusColor] }}" 
                     style="width: {{ $percentage }}%"></div>
            </div>
        </div>
        
        @if($percentage >= 90)
            <div class="mt-2 text-xs">
                @if($percentage >= 100)
                    <span class="font-medium">Limit reached!</span>
                    <a href="{{ route('subscription.plans') }}" class="underline hover:no-underline">Upgrade to continue</a>
                @else
                    <span class="font-medium">Almost at limit.</span>
                    <a href="{{ route('subscription.plans') }}" class="underline hover:no-underline">Consider upgrading</a>
                @endif
            </div>
        @endif
    @endif
</div>
