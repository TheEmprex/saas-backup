@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Edit User Subscription</h2>
        
        <form method="POST" action="{{ route('admin.users.subscription.update', $user) }}">
            @csrf
            <div class="mb-6">
                <label for="subscription_plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Subscription Plan</label>
                <select name="subscription_plan_id" id="subscription_plan_id" class="mt-1 block w-full bg-white dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600 rounded-md">
                    @foreach($subscriptionPlans as $plan)
                        <option value="{{ $plan->id }}" {{ $user->currentSubscriptionPlan && $user->currentSubscriptionPlan->id == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }} - ${{ number_format($plan->price, 2) }} / {{ $plan->interval }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    Update Subscription
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

