<?php

use App\Models\Link;

test('redirects a short code to its destination', function () {
    $link = Link::factory()->create(['destination' => 'https://laravel.com/docs']);

    $this->get(route('links.redirect', $link->code))
        ->assertRedirect('https://laravel.com/docs');
});

test('counts the visit', function () {
    $link = Link::factory()->create();

    $this->get(route('links.redirect', $link->code))->assertRedirect();

    expect($link->refresh())
        ->visits->toBe(1)
        ->last_visited_at->not->toBeNull();
});

test('returns not found for an unknown code', function () {
    $this->get('/does-not-exist')->assertNotFound();
});

test('does not shadow the api routes', function () {
    $this->getJson(route('links.index'))->assertOk();
});

test('does not shadow the health check route', function () {
    $this->get('/up')->assertOk();
});
