<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;

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



/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']); // Accessible uniquement par les admins
});

Route::middleware(['auth:sanctum', 'organisateur'])->group(function () {
    Route::post('/events/create', [OrganisateurController::class, 'store']); // Accessible uniquement par les organisateurs
    Route::post('/addEvents', [EventController::class, 'store']);  // Créer un événement
    Route::put('/updateEvents/{id}', [EventController::class, 'update']);  // Modifier un événement
    Route::delete('/deleteEvents/{id}', [EventController::class, 'destroy']);  // Supprimer un événement
});

Route::middleware(['auth:sanctum', 'participant'])->group(function () {
    Route::get('/events', [ParticipantController::class, 'index']); // Accessible uniquement par les participants
});

// Accessible à tous les utilisateurs authentifiés
Route::middleware('auth:sanctum')->get('/events', [EventController::class, 'index']); // Voir tous les événements
Route::middleware('auth:sanctum')->put('/update-profile', [AuthController::class, 'updateProfile']);

