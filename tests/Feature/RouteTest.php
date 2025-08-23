<?php

// tests/Feature/RouteResponseTest.php

use function Pest\Laravel\get;

it('responds with 200 for all routes', function (string $route) {
    $response = get($route);
    $response->assertStatus(200);
})->with('routes');

// Skip auth route tests for now due to database setup complexity
// They can be enabled once proper test database seeding is set up
