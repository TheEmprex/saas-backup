<x-theme::layouts.app>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">My Training Progress</h1>

    <!-- Progress Summary -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Summary</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Completed</div>
                <div class="text-2xl font-bold text-gray-900">{{ $progress['completed']->count() ?? 0 }} modules</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">In Progress</div>
                <div class="text-2xl font-bold text-gray-900">{{ $progress['in_progress']->count() ?? 0 }} modules</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Not Started</div>
                <div class="text-2xl font-bold text-gray-900">{{ $progress['not_started']->count() ?? 0 }} modules</div>
            </div>
        </div>
    </div>

    <!-- Detailed Progress -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Detailed Progress</h2>
        <div class="space-y-4">
            @if(isset($testResults) && $testResults->count() > 0)
                @foreach($testResults as $result)
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $result->testable->trainingModule->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $result->testable->title }}</p>
                            </div>
                            <div>
                                <span class="text-lg font-semibold {{ $result->passed ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($result->score, 0) }}%
                                </span>
                                <span class="ml-2 text-sm text-gray-500">({{ $result->passed ? 'Passed' : 'Failed' }})</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center text-gray-500">No training test results yet.</p>
            @endif
        </div>
    </div>
</div>
</x-theme::layouts.app>
