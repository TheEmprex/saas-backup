<?php

test('subscription dashboard requires authentication', function () {
    $response = $this->get('/subscription/dashboard');
    // Should redirect to login when not authenticated
    $response->assertRedirect();
});

test('pricing page is accessible', function () {
    $response = $this->get('/pricing');
    $response->assertStatus(200);
});

// Database-dependent tests disabled for now
// TODO: Add proper database seeding for subscription tests
