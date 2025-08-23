@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Review Contests Management</h2>

        <form method="GET" action="{{ route('admin.contests.index') }}" class="mb-6">
            <div class="flex items-center space-x-4">
                <div class="flex-grow">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user name or email"
                           class="w-full border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"/>
                </div>
                <div>
                    <select name="status" class="border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Filter</button>
            </div>
        </form>

        @if($contests->count() > 0)
            <div class="space-y-4">
                @foreach($contests as $contest)
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 border border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Contest for rating by {{ $contest->rating->rater->name }}
                                </h4>
                                <div class="flex items-center mt-2">
                                    @for ($i = 0; $i < 5; $i++)
                                        @if ($i < $contest->rating->overall_rating)
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $contest->rating->overall_rating }}/5</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($contest->reason, 100) }}</p>
                            </div>
                            <div class="ml-4 flex flex-col items-end">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $contest->status === 'pending' ? 'yellow' : ($contest->status === 'approved' ? 'green' : 'red') }}-100 dark:bg-{{ $contest->status === 'pending' ? 'yellow' : ($contest->status === 'approved' ? 'green' : 'red') }}-900/30 text-{{ $contest->status === 'pending' ? 'yellow' : ($contest->status === 'approved' ? 'green' : 'red') }}-800 dark:text-{{ $contest->status === 'pending' ? 'yellow' : ($contest->status === 'approved' ? 'green' : 'red') }}-200 border border-{{ $contest->status === 'pending' ? 'yellow' : ($contest->status === 'approved' ? 'green' : 'red') }}-200 dark:border-{{ $contest->status === 'pending' ? 'yellow' : ($contest->status === 'approved' ? 'green' : 'red') }}-700">
                                    {{ ucfirst($contest->status) }}
                                </span>
                                <div class="mt-2 flex space-x-2">
                                    <a href="{{ route('admin.contests.show', $contest) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Submitted {{ $contest->created_at->diffForHumans() }}
                            @if($contest->resolved_at)
                                • Resolved {{ $contest->resolved_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($contests->hasPages())
                <div class="mt-6">
                    {{ $contests->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No contests found</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">There are currently no review contests to display. As contests are submitted, they will appear here for your review and management.</p>
            </div>
        @endif
    </div>
</div>
@endsection

