<?php
// Debug script to test job creation route
echo "Testing 'Post a new job' button issue...\n\n";

// Test 1: Route exists
echo "1. Testing route existence:\n";
$output = shell_exec('cd /Users/Maxou/saas && php artisan route:list --name=marketplace.jobs.create');
echo $output . "\n";

// Test 2: Route redirect (unauthenticated)
echo "2. Testing unauthenticated access:\n";
$output = shell_exec('curl -w "HTTP Status: %{http_code}\nRedirect Location: %{redirect_url}" -s -o /dev/null "http://localhost:8000/marketplace/jobs/create"');
echo $output . "\n";

// Test 3: Login page availability
echo "3. Testing login page:\n";
$output = shell_exec('curl -w "HTTP Status: %{http_code}" -s -o /dev/null "http://localhost:8000/custom/login"');
echo $output . "\n";

// Test 4: Check if Laravel server is running
echo "4. Testing Laravel server:\n";
$output = shell_exec('curl -w "HTTP Status: %{http_code}" -s -o /dev/null "http://localhost:8000"');
echo $output . "\n";

echo "Debug complete. If all tests show 200/302 status codes, the issue is likely in the browser.\n";
echo "Please check browser console for JavaScript errors when clicking the button.\n";
