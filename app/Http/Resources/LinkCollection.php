<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class LinkCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var class-string<LinkResource>
     */
    public $collects = LinkResource::class;

    /**
     * Trim Laravel's default pagination payload.
     *
     * The default adds a "links" block plus a "meta.links" array of Blade
     * pagination buttons (labels such as "&laquo; Previous"), which a separate
     * front-end never renders. Only the page counters are kept.
     *
     * @param  array<string, mixed>  $paginated
     * @param  array<string, mixed>  $default
     * @return array<string, mixed>
     */
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        return [
            'meta' => Arr::only($paginated, [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ]),
        ];
    }
}
