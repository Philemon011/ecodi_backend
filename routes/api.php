<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/home', function (Request $request) {
    return response([
        'success' => true,
        'message' => "Welcom",
    ], 200);
});

Route::apiResource('/cours', App\Http\Controllers\CoursController::class);
Route::apiResource('/audio', App\Http\Controllers\AudioController::class);

// Route::put('/coursmodif/{id}', [App\Http\Controllers\CoursController::class, 'modifCours']);





