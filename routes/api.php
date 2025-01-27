<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Espace administrateur']);
    });
});

Route::middleware(['auth:sanctum', 'role:organisateur'])->group(function () {
    Route::get('/organisateur/dashboard', function () {
        return response()->json(['message' => 'Espace organisateur']);
    });
});

Route::middleware(['auth:sanctum', 'role:participant'])->group(function () {
    Route::get('/participant/dashboard', function () {
        return response()->json(['message' => 'Espace participant']);
    });
});


Route::middleware('auth:sanctum')->put('/update-profile', [AuthController::class, 'updateProfile']);

