<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    // ------------------------- Créer un événement--------------------------------

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'organisateur') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'required|string',
            'date' => 'required|date',
            'max_participants' => 'required|integer|min:1',
            'categories' => 'required|array', // Liste des catégories
            'categories.*' => 'exists:categories,id', // Chaque catégorie doit exister
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()], 422);
        }

        // Gérer l'image
        $path = $request->file('image')->store('events', 'public');

        // Créer l'événement
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $path,
            'location' => $request->location,
            'date' => $request->date,
            'max_participants' => $request->max_participants,
            'organizer_id' => auth()->id(),
        ]);

        // Attacher les catégories à l'événement
        $event->categories()->sync($request->categories);

        return response()->json(['message' => 'Événement créé avec succès!', 'event' => $event]);
    }

    // ------------------------------ Modifier un événement------------------------------------

    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['error' => 'Événement introuvable'], 404);
        }

        if (auth()->user()->id !== $event->organizer_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'sometimes|string',
            'date' => 'sometimes|date',
            'max_participants' => 'sometimes|integer|min:1',
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()], 422);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $event->image = $path;
        }

        $event->update($request->except('categories'));

        if ($request->has('categories')) {
            $event->categories()->sync($request->categories);
        }

        return response()->json(['message' => 'Événement mis à jour avec succès!', 'event' => $event]);
    }

    // ----------------------------- Supprimer un événement------------------------------------
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['error' => 'Événement introuvable'], 404);
        }

        if (auth()->user()->id !== $event->organizer_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json(['message' => 'Événement supprimé avec succès!']);
    }

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
