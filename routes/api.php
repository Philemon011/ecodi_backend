<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/home', function (Request $request) {
    return response([
        'success' => true,
        'message' => "le  Signalement a été bien modifié !",
    ], 200);
});


