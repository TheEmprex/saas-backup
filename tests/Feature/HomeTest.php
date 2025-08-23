<?php

it('Home returns a successful response', function () {
    $response = $this->get('/');
    $response->assertStatus(302);
    $response->assertRedirect('/marketplace');
});
