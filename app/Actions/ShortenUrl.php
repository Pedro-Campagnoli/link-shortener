<?php

namespace App\Actions;

use App\Models\Link;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use RuntimeException;

class ShortenUrl
{
    /**
     * Number of characters in a generated code.
     */
    private const CODE_LENGTH = 7;

    /**
     * How many times to retry when a generated code is already taken.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Store a destination URL behind a short code.
     *
     * The unique index on "code" is the source of truth: a colliding insert is
     * retried with a new code rather than checked for beforehand, which would
     * leave a race between the check and the insert.
     */
    public function handle(string $destination, ?string $code = null): Link
    {
        if ($code !== null) {
            return Link::create([
                'code' => $code,
                'destination' => $destination,
            ]);
        }

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            try {
                return Link::create([
                    'code' => Str::random(self::CODE_LENGTH),
                    'destination' => $destination,
                ]);
            } catch (UniqueConstraintViolationException) {
                continue;
            }
        }

        throw new RuntimeException(sprintf(
            'Could not generate an unused link code in %d attempts.',
            self::MAX_ATTEMPTS,
        ));
    }
}
