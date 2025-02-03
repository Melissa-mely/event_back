<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;



class ParticipantController extends Controller
{
       // ------------------- S'inscrire a un evenément ---------------------------------------

       public function register(Request $request, $eventId)
       {
           if (auth()->user()->role !== 'participant') {
               return response()->json(['error' => 'Unauthorized'], 403);
           }
           $user = auth()->user();
           
           
           $event = Event::findOrFail($eventId);
           
           // Vérifier si l'utilisateur est déjà inscrit
           if ($event->participants()->where('user_id', $user->id)->exists()) {
               return response()->json(['message' => 'Vous êtes déjà inscrit à cet événement.'], 400);
           }
           
           // Ajouter l'utilisateur à l'événement
           $event->participants()->attach($user->id);
       
           return response()->json(['message' => 'Inscription réussie !']);
       }
       
       
        // ------------------- se desinscrire d'un evenément ---------------------------------------
       
       public function unregister(Request $request, $eventId)
       {
           $user = auth()->user();
       
           $event = Event::findOrFail($eventId);
       
           // Vérifier si l'utilisateur est inscrit
           if (!$event->participants()->where('user_id', $user->id)->exists()) {
               return response()->json(['message' => 'Vous n\'êtes pas inscrit à cet événement.'], 400);
           }
       
           // Supprimer l'inscription
           $event->participants()->detach($user->id);
       
           return response()->json(['message' => 'Désinscription réussie.']);
       }
       
       // ------------------- ajouter un evenement aux favoris---------------------------------------
       
       public function addFavorite(Request $request, $eventId)
       {
           $user = auth()->user();
           
           $event = Event::findOrFail($eventId);
       
           // Vérifier si l'événement est déjà en favoris
           if ($user->favorites()->where('event_id', $event->id)->exists()) {
               return response()->json(['message' => 'Événement déjà en favoris.'], 400);
           }
       
           $user->favorites()->attach($event->id);
       
           return response()->json(['message' => 'Ajouté aux favoris.']);
       }
       
       
       // ------------------- retirer un evenement des favoris---------------------------------------
       
       public function removeFavorite(Request $request, $eventId)
       {
           $user = auth()->user();
           
           $event = Event::findOrFail($eventId);
       
           $user->favorites()->detach($event->id);
       
           return response()->json(['message' => 'Supprimé des favoris.']);
       }
       
       
       // ------------------- la liste des événements auxquels l'utilisateur est inscrit--------------------------------------
       
       public function myEvents()
       {
           $user = auth()->user();
       
           // Récupérer les événements auxquels l'utilisateur est inscrit
           $events = $user->participatedEvents()->get();
       
           return response()->json([
               'success' => true,
               'events' => $events
           ]);
       }
       
       // ------------------- la liste des événements favoris de l'utilisateur--------------------------------------
       
       public function myFavorites()
       {
           $user = auth()->user();
       
           // Récupérer les événements favoris de l'utilisateur
           $favorites = $user->favorites()->get();
       
           return response()->json([
               'success' => true,
               'favorites' => $favorites
           ]);
       }
       
       
}
