<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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

        // Gérer l'upload vers Cloudinary
if ($request->hasFile('image')) {
    $uploadedFile = $request->file('image');

    $uploadedImage = Cloudinary::upload($uploadedFile->getRealPath(), [
        'folder' => 'events'
    ]);

    $imageUrl = $uploadedImage->getSecurePath();
} else {
    $imageUrl = null;
}

        // Créer l'événement
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageUrl,
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
    
        // Vérifier si une nouvelle image est envoyée
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image de Cloudinary (Optionnel : à condition de stocker l'ID public de l'image)
            if ($event->image) {
                $publicId = pathinfo($event->image, PATHINFO_FILENAME);
                Cloudinary::destroy("events/{$publicId}");
            }
    
            // Téléverser la nouvelle image sur Cloudinary
            $uploadedFile = $request->file('image');
            $uploadedImage = Cloudinary::upload($uploadedFile->getRealPath(), [
                'folder' => 'events'
            ]);
    
            // Mettre à jour l'URL de l'image
            $event->image = $uploadedImage->getSecurePath();
        }
    
        // Mettre à jour les autres champs
        $event->update($request->except('categories', 'image'));
    
        // Mettre à jour les catégories si fournies
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

  

// ------------------- voir les evenements d'un organisateur ---------------------------------------
public function getOrganizerEvents()
{
    // Vérifie si l'utilisateur est authentifié et qu'il est un organisateur
    if (auth()->user()->role !== 'organisateur') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Récupérer les événements de l'organisateur connecté
    $events = Event::where('organizer_id', auth()->id())
                    ->with(['categories', 'organizer'])
                    ->get();

    return response()->json([
        'success' => true,
        'data' => $events
    ]);
}

// ------------------- voir les participants d'un evenement ---------------------------------------
public function getEventParticipants($id)
{
    // Vérifier si l'événement existe
    $event = Event::with('participants')->find($id);

    if (!$event) {
        return response()->json(['error' => 'Événement non trouvé'], 404);
    }

    // Vérifier si l'organisateur de l'événement est l'utilisateur connecté
    if (auth()->user()->id !== $event->organizer_id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Récupérer les participants de l'événement
    $participants = $event->participants;  // Cela donne la liste des utilisateurs associés à l'événement

    return response()->json([
        'success' => true,
        'participants' => $participants
    ]);
}



 
}
