<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AdminController;



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
Route::get('/events', [IndexController::class, 'index']); // Voir TOUS les événements avec leurs organisateurs et catégories
Route::get('/events/{id}', [IndexController::class, 'show']);// Voir UN événement avec ses catégories et organisateur
Route::get('/search', [IndexController::class, 'search']);// Rechercher des événements par mot-clé
Route::get('/events/by-category/{categoryId}', [IndexController::class, 'filterByCategory']);// Filtrer les événements par catégorie
Route::get('/upcoming', [IndexController::class, 'getUpcomingEvents']);// Voir les événements à venir
Route::get('/categories', [CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->put('/update-profile', [AuthController::class, 'updateProfile']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::delete('/admin/event/{id}', [AdminController::class, 'deleteEvent']); // Supprimer un événement
    Route::delete('/admin/organizer/{id}', [AdminController::class, 'deleteOrganizer']); // Supprimer un organisateur et ses events
    Route::get('/organizers', [adminController::class, 'listOrganizers']); // Voir les organisateurs
});

Route::middleware(['auth:sanctum', 'organisateur'])->group(function () {
    Route::post('/events/create', [OrganisateurController::class, 'store']); // Accessible uniquement par les organisateurs
    Route::post('/addEvents', [EventController::class, 'store']);  // Créer un événement
    Route::put('/updateEvents/{id}', [EventController::class, 'update']);  // Modifier un événement
    Route::delete('/deleteEvents/{id}', [EventController::class, 'destroy']);  // Supprimer un événement
    Route::get('/organizer/events', [EventController::class, 'getOrganizerEvents']);// Voir les événements créés par l'organisateur
    Route::get('/event/{id}/participants', [EventController::class, 'getEventParticipants']);// Voir les participants à un événement d'un organisateur
});

Route::middleware(['auth:sanctum', 'participant'])->group(function () {
    Route::post('/events/{event}/register', [ParticipantController::class, 'register']); // S'inscrire à un événement
    Route::delete('/events/{event}/unregister', [ParticipantController::class, 'unregister']); // Se désinscrire d'un événement
    Route::post('/events/{event}/favorite', [ParticipantController::class, 'addFavorite']);//ajouter un evenement en favoris
    Route::delete('/events/{event}/unfavorite', [ParticipantController::class, 'removeFavorite']);//retirer un evenement des favoris
    Route::get('/my-events', [ParticipantController::class, 'myEvents']); // Voir les événements aux quels je me suis inscrit
    Route::get('/my-favorites', [ParticipantController::class, 'myFavorites']);   // Voir les événements en favoris

});

// Accessible à tous les utilisateurs authentifiés
#Route::middleware('auth:sanctum')->get('/events', [EventController::class, 'index']); // Voir tous les événements


