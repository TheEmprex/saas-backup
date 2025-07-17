<x-layouts.marketing
    :seo="[
        'title'         => 'Payment Required - OnlyFans Management Marketplace',
        'description'   => 'Complete payment for your job posting features.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Your Payment</h1>
            <p class="text-gray-600">Payment required for job posting features</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-semibold mb-4">Order Summary</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Job Title:</span>
                    <span class="font-medium">{{ $jobData['title'] }}</span>
                </div>
                @if($featuredCost > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Featured Job:</span>
                        <span class="font-medium">${{ $featuredCost }}</span>
                    </div>
                @endif
                @if($urgentCost > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Urgent Badge:</span>
                        <span class="font-medium">${{ $urgentCost }}</span>
                    </div>
                @endif
                <div class="border-t pt-2 mt-4">
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total:</span>
                        <span>${{ $totalCost }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('marketplace.jobs.create') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <form action="{{ route('job.payment.process') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Pay Now (${{ $totalCost }})
                </button>
            </form>
        </div>
    </div>
</div>

</x-layouts.marketing>
