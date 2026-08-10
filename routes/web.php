<?php

use App\Http\Controllers\RedirectShortLinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| The front-end lives in a separate project. Only routes a browser hits
| directly belong here — such as the short link redirect below, which must
| stay the last route registered so it never shadows a real path.
|
*/

Route::get('/', fn () => response()->json([
    'application' => config('app.name'),
]))->name('home');

Route::get('/{link}', RedirectShortLinkController::class)
    ->where('link', '[A-Za-z0-9_-]+')
    ->name('links.redirect');
