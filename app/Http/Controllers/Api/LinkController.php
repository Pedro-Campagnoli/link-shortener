<?php

namespace App\Http\Controllers\Api;

use App\Actions\ShortenUrl;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Http\Resources\LinkCollection;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

class LinkController extends Controller
{
    public function __construct(private readonly ShortenUrl $shortenUrl) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): LinkCollection
    {
        return new LinkCollection(Link::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLinkRequest $request): JsonResponse
    {
        $link = $this->shortenUrl->handle(
            destination: $request->string('destination')->toString(),
            code: $request->filled('code') ? $request->string('code')->toString() : null,
        );

        return (new LinkResource($link))
            ->response()
            ->setStatusCode(HttpStatus::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Link $link): LinkResource
    {
        return new LinkResource($link);
    }

    /**
     * Update the specified resource in storage.
     *
     * Only the destination changes: the code, and therefore every short URL
     * already shared, keeps pointing at this link. Visit counts are kept too.
     */
    public function update(UpdateLinkRequest $request, Link $link): LinkResource
    {
        $link->update($request->validated());

        return new LinkResource($link);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Link $link): Response
    {
        $link->delete();

        return response()->noContent();
    }
}
