@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Review Contest Details</h2>

        <div class="mb-6">
            <h4 class="font-semibold text-lg text-gray-900 dark:text-white">Contest by: {{ $contest->contestedBy->name }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $contest->contestedBy->email }}</p>
        </div>

        <div class="mb-6">
            <h4 class="font-semibold text-lg text-gray-900 dark:text-white">Rating for: {{ $contest->rating->rated->name }}</h4>
            <div class="flex items-center">
                @for ($i = 0; $i < 5; $i++)
                    @if ($i < $contest->rating->overall_rating)
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endif
                @endfor
            </div>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $contest->rating->review_content }}</p>
        </div>

        <div class="mb-4">
            <h4 class="font-semibold text-lg text-gray-900 dark:text-white">Contest Details</h4>
            <p class="text-gray-600 dark:text-gray-400"><strong>Status:</strong> 
              @if($contest->isPending())
                <span class="text-yellow-500">Pending</span>
              @elseif($contest->isApproved())
                <span class="text-green-500">Approved</span>
              @elseif($contest->isRejected())
                <span class="text-red-500">Rejected</span>
              @endif
            </p>
            <p class="text-gray-600 dark:text-gray-400"><strong>Reason:</strong> {{ $contest->reason }}</p>
            @if($contest->evidence)
                <p class="text-gray-600 dark:text-gray-400"><strong>Evidence:</strong> {{ $contest->evidence }}</p>
            @endif
            @if($contest->admin_notes)
                <p class="text-gray-600 dark:text-gray-400"><strong>Admin Notes:</strong> {{ $contest->admin_notes }}</p>
            @endif
            <p class="text-gray-600 dark:text-gray-400"><strong>Submitted:</strong> {{ $contest->created_at->format('Y-m-d H:i:s') }}</p>
            @if($contest->resolved_at)
                <p class="text-gray-600 dark:text-gray-400"><strong>Resolved:</strong> {{ $contest->resolved_at->format('Y-m-d H:i:s') }}</p>
            @endif
        </div>

        @if($contest->isPending())
        <div class="flex justify-end space-x-4">
            <form method="POST" action="{{ route('admin.contests.approve', $contest) }}" class="inline">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Admin Notes (optional)</label>
                    <textarea name="admin_notes" rows="2" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" placeholder="Add any notes about your decision..."></textarea>
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
                    Approve Contest (Remove Rating)
                </button>
            </form>
            
            <form method="POST" action="{{ route('admin.contests.reject', $contest) }}" class="inline">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Admin Notes (required)</label>
                    <textarea name="admin_notes" rows="2" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" placeholder="Please explain why the contest is being rejected..." required></textarea>
                </div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                    Reject Contest
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
