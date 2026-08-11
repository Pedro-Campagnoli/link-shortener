<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\RedirectResponse;

class RedirectShortLinkController extends Controller
{
    /**
     * The redirect is a 302 on purpose: a 301 gets cached by browsers, which
     * would both freeze the destination and stop the visit counter.
     */
    public function __invoke(Link $link): RedirectResponse
    {
        defer(fn () => $link->recordVisit());

        return redirect()->away($link->destination);
    }
}
