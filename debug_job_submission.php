<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Start a console session
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Debug information
echo "=== DEBUG JOB SUBMISSION ===\n";

// Check if we have users
$userCount = App\Models\User::count();
echo "Total users: " . $userCount . "\n";

if ($userCount > 0) {
    $user = App\Models\User::first();
    echo "First user: " . $user->name . " (ID: " . $user->id . ")\n";
    
    // Check user verification status
    if ($user->userType) {
        echo "User type: " . $user->userType->name . "\n";
    } else {
        echo "No user type set\n";
    }
    
    echo "Has KYC submitted: " . ($user->hasKycSubmitted() ? 'YES' : 'NO') . "\n";
    echo "Is KYC verified: " . ($user->isKycVerified() ? 'YES' : 'NO') . "\n";
    echo "Requires verification: " . ($user->requiresVerification() ? 'YES' : 'NO') . "\n";
    echo "Can post job: " . ($user->canPostJob() ? 'YES' : 'NO') . "\n";
} else {
    echo "No users found\n";
}

// Test route existence
try {
    $route = Route::getRoutes()->getByName('marketplace.jobs.store');
    echo "Route 'marketplace.jobs.store' exists: YES\n";
    echo "Route URI: " . $route->uri() . "\n";
    echo "Route methods: " . implode(',', $route->methods()) . "\n";
    echo "Route middleware: " . implode(',', $route->middleware()) . "\n";
} catch (Exception $e) {
    echo "Route 'marketplace.jobs.store' exists: NO - " . $e->getMessage() . "\n";
}

// Test test-ajax route
try {
    $route = Route::getRoutes()->getByName('test.ajax');
    echo "Route 'test.ajax' exists: YES\n";
    echo "Route URI: " . $route->uri() . "\n";
} catch (Exception $e) {
    echo "Route 'test.ajax' exists: NO - " . $e->getMessage() . "\n";
}

echo "=== END DEBUG ===\n";
