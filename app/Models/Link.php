<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $destination
 * @property int $visits
 * @property Carbon|null $last_visited_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $short_url
 */
#[Appends(['short_url'])]
#[Fillable(['code', 'destination'])]
#[RouteKey('code')]
class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory;

    protected $attributes = [
        'visits' => 0,
    ];

    protected function casts(): array
    {
        return [
            'last_visited_at' => 'datetime',
            'visits' => 'integer',
        ];
    }

    /** @return Attribute<string, never> */
    protected function shortUrl(): Attribute
    {
        return Attribute::get(fn (): string => route('links.redirect', $this->code));
    }

    public function recordVisit(): void
    {
        $this->increment('visits', 1, ['last_visited_at' => now()]);
    }
}
