<x-theme::layouts.app>
<div class="p-8">
    <h1 class="text-2xl font-bold mb-4">DEBUG: Marketplace Jobs</h1>
    
    <div class="mb-4">
        <p><strong>Jobs Count:</strong> {{ $jobs->count() }}</p>
        <p><strong>Jobs Total:</strong> {{ $jobs->total() }}</p>
    </div>
    
    @if($jobs->count() > 0)
        <div class="space-y-4">
            @foreach($jobs as $index => $job)
                <div class="border p-4 rounded bg-gray-100">
                    <p><strong>Job #{{ $index + 1 }}:</strong></p>
                    <h3 class="font-bold text-blue-600">{{ $job->title ?? 'NO TITLE' }}</h3>
                    <p>ID: {{ $job->id ?? 'NO ID' }}</p>
                    <p>Status: {{ $job->status ?? 'NO STATUS' }}</p>
                    <p>Market: {{ $job->market ?? 'NO MARKET' }}</p>
                    <p>By: {{ $job->user ? $job->user->name : 'NO USER' }}</p>
                    <p>Rate: ${{ $job->hourly_rate ?? $job->fixed_rate ?? '0' }}</p>
                    <p>Applications: {{ $job->applications ? $job->applications->count() : 'NO APPS' }}</p>
                    <p>Created: {{ $job->created_at ?? 'NO DATE' }}</p>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-red-500 text-xl">NO JOBS FOUND!</div>
    @endif
    
    <div class="mt-4">
        <p><strong>Finished jobs loop.</strong></p>
    </div>
</div>
</x-theme::layouts.app>
