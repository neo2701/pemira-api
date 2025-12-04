<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/auth/login', [AuthController::class, 'loginTest']);
// Route::get('/auth/callback', [AuthController::class, 'callback']);

Route::get("/", function () {
    return redirect()->away(env("APP_CLIENT_URL", "https://pemiraif.com"));
});

// handle non-existing routes
Route::fallback(function () {
    return response()->json(
        [
            "message" => "Resource not found.",
        ],
        404,
    );
});
