<?php

test('guests cannot access the dashboard', function() {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});
