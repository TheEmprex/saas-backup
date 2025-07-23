@extends('theme::app')

@section('content')
cdiv class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8"e
    cdiv class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"e
        c!-- Header --e
        cdiv class="mb-8"e
            cdiv class="flex items-center justify-between"e
                cdive
                    ch1 class="text-3xl font-bold text-gray-900 dark:text-white"eKYC Verificationc/h1e
                    cp class="text-gray-600 dark:text-gray-400 mt-1"eReview and approve KYC submissionsc/pe
                c/dive
                ca href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg"e
                    ← Back to Dashboard
                c/ae
            c/dive
        c/dive

        c!-- Filters --e
        cdiv class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6"e
            cform method="GET" action="{{ route('admin.kyc.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4"e
                cdive
                    clabel for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"eStatusc/labele
                    cselect id="status" name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"e
                        coption value=""eAll Statusc/optione
                        coption value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}ePendingc/optione
                        coption value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}eApprovedc/optione
                        coption value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}eRejectedc/optione
                    c/selecte
                c/dive
                cdiv class="flex items-end"e
                    cbutton type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"e
                        Filter
                    c/buttone
                    ca href="{{ route('admin.kyc.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg"e
                        Clear
                    c/ae
                c/dive
            c/forme
        c/dive

        c!-- KYC Requests Table --e
        cdiv class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"e
            cdiv class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"e
                ch2 class="text-lg font-semibold text-gray-900 dark:text-white"eKYC Requestsc/h2e
            c/dive
            
            @if($verifications->count() > 0)
                cdiv class="overflow-x-auto"e
                    ctable class="w-full"e
                        cthead class="bg-gray-50 dark:bg-gray-700"e
                            ctre
                                cth class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"eUserc/the
                                cth class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"eStatusc/the
                                cth class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"eSubmittedc/the
                                cth class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"eActionsc/the
                            c/tre
                        c/theade
                        ctbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"e
                            @foreach($verifications as $verification)
                                ctr class="hover:bg-gray-50 dark:hover:bg-gray-700"e
                                    ctd class="px-6 py-4"e
                                        cdiv class="text-sm font-medium text-gray-900 dark:text-white"e
                                            {{ $verification->user->name }}
                                        c/dive
                                        cdiv class="text-sm text-gray-500 dark:text-gray-400"e
                                            {{ $verification->user->email }}
                                        c/dive
                                    c/tde
                                    ctd class="px-6 py-4 whitespace-nowrap"e
                                        cspan class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($verification->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($verification->status == 'approved') bg-green-100 text-green-800
                                            @elseif($verification->status == 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif"e
                                            {{ ucfirst($verification->status) }}
                                        c/spane
                                    c/tde
                                    ctd class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"e
                                        {{ $verification->created_at->format('M d, Y') }}
                                    c/tde
                                    ctd class="px-6 py-4 whitespace-nowrap text-sm font-medium"e
                                        cdiv class="flex space-x-2"e
                                            ca href="{{ route('admin.kyc.show', $verification) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400"e
                                                View
                                            c/ae
                                            cform method="POST" action="{{ route('admin.kyc.update-status', $verification) }}" class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to change the status of this KYC verification?')"e
                                                @csrf
                                                @method('PATCH')
                                                cbutton type="submit" name="status" value="approved" class="text-green-600 hover:text-green-900 dark:text-green-400"e
                                                    Approve
                                                c/buttone
                                                cbutton type="submit" name="status" value="rejected" class="text-red-600 hover:text-red-900 dark:text-red-400"e
                                                    Reject
                                                c/buttone
                                            c/forme
                                        c/dive
                                    c/tde
                                c/tre
                            @endforeach
                        c/tbodye
                    c/tablee
                c/dive
                
                c!-- Pagination --e
                cdiv class="px-6 py-4 border-t border-gray-200 dark:border-gray-700"e
                    {{ $verifications->withQueryString()->links() }}
                c/dive
            @else
                cdiv class="p-12 text-center"e
                    csvg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"e
                        cpath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"ec/pathe
                    c/svge
                    ch3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white"eNo KYC requests foundc/h3e
                    cp class="mt-1 text-sm text-gray-500 dark:text-gray-400"eNo verifications match your current filters.c/pe
                c/dive
            @endif
        c/dive
    c/dive
c/dive
@endsection

