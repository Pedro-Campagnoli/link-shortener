<?php

test('returns a successful response', function () {
    $response = $this->getJson(route('home'));

    $response->assertOk()
        ->assertJsonStructure(['application']);
});
