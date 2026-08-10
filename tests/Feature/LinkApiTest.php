<?php

use App\Models\Link;

test('shortens a url and returns the generated code', function () {
    $response = $this->postJson(route('links.store'), [
        'destination' => 'https://laravel.com/docs',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.destination', 'https://laravel.com/docs')
        ->assertJsonPath('data.visits', 0);

    $link = Link::sole();

    expect($link->code)->toHaveLength(7)
        ->and($response->json('data.short_url'))->toBe(route('links.redirect', $link->code));
});

test('accepts a custom code', function () {
    $this->postJson(route('links.store'), [
        'destination' => 'https://laravel.com/docs',
        'code' => 'laravel-docs',
    ])->assertCreated()
        ->assertJsonPath('data.code', 'laravel-docs');
});

test('rejects a code that is already taken', function () {
    Link::factory()->create(['code' => 'taken']);

    $this->postJson(route('links.store'), [
        'destination' => 'https://laravel.com/docs',
        'code' => 'taken',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('code');
});

test('rejects invalid input', function (array $payload, string $invalidField) {
    $this->postJson(route('links.store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($invalidField);
})->with([
    'missing destination' => [[], 'destination'],
    'destination is not a url' => [['destination' => 'not-a-url'], 'destination'],
    'destination is not http' => [['destination' => 'ftp://example.com'], 'destination'],
    'code is too short' => [['destination' => 'https://example.com', 'code' => 'ab'], 'code'],
    'code has invalid characters' => [['destination' => 'https://example.com', 'code' => 'não/ok'], 'code'],
    'code shadows a route' => [['destination' => 'https://example.com', 'code' => 'api'], 'code'],
]);

test('lists links newest first', function () {
    $older = Link::factory()->create(['created_at' => now()->subDay()]);
    $newer = Link::factory()->create();

    $this->getJson(route('links.index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.code', $newer->code)
        ->assertJsonPath('data.1.code', $older->code);
});

test('returns only the page counters alongside the data', function () {
    Link::factory()->count(2)->create();

    $this->getJson(route('links.index'))
        ->assertOk()
        ->assertExactJsonStructure([
            'data' => ['*' => ['code', 'short_url', 'destination', 'visits', 'last_visited_at', 'created_at']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ])
        ->assertJsonPath('meta.total', 2)
        ->assertJsonPath('meta.per_page', 15);
});

test('shows a link with its visit stats', function () {
    $link = Link::factory()->visited(12)->create();

    $this->getJson(route('links.show', $link))
        ->assertOk()
        ->assertJsonPath('data.code', $link->code)
        ->assertJsonPath('data.visits', 12);
});

test('returns not found for an unknown code', function () {
    $this->getJson(route('links.show', 'nope123'))->assertNotFound();
});

test('updates the destination without touching the code or the visit count', function () {
    $link = Link::factory()->visited(9)->create([
        'code' => 'keep-me',
        'destination' => 'https://old.example.com',
    ]);

    $this->putJson(route('links.update', $link), [
        'destination' => 'https://new.example.com/page',
    ])->assertOk()
        ->assertJsonPath('data.code', 'keep-me')
        ->assertJsonPath('data.destination', 'https://new.example.com/page')
        ->assertJsonPath('data.visits', 9);

    expect($link->refresh()->destination)->toBe('https://new.example.com/page');
});

test('redirects to the new destination after an update', function () {
    $link = Link::factory()->create(['destination' => 'https://old.example.com']);

    $this->putJson(route('links.update', $link), [
        'destination' => 'https://new.example.com',
    ])->assertOk();

    $this->get(route('links.redirect', $link->code))
        ->assertRedirect('https://new.example.com');
});

test('ignores an attempt to change the code', function () {
    $link = Link::factory()->create(['code' => 'original']);

    $this->putJson(route('links.update', $link), [
        'destination' => 'https://example.com',
        'code' => 'hijacked',
    ])->assertOk()
        ->assertJsonPath('data.code', 'original');

    expect($link->refresh()->code)->toBe('original');
});

test('rejects an invalid destination on update', function (array $payload) {
    $link = Link::factory()->create();

    $this->putJson(route('links.update', $link), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('destination');
})->with([
    'missing' => [[]],
    'not a url' => [['destination' => 'not-a-url']],
    'not http' => [['destination' => 'ftp://example.com']],
]);

test('returns not found when updating an unknown code', function () {
    $this->putJson(route('links.update', 'nope123'), [
        'destination' => 'https://example.com',
    ])->assertNotFound();
});

test('deletes a link', function () {
    $link = Link::factory()->create();

    $this->deleteJson(route('links.destroy', $link))->assertNoContent();

    $this->assertModelMissing($link);
});
