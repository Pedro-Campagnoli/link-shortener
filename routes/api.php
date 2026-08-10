<?php

use App\Http\Controllers\Api\LinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('links', LinkController::class)
    ->middlewareFor('store', 'throttle:shorten');
