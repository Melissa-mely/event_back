<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Méthode pour supprimer un événement
    public function deleteEvent($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['error' => 'Événement introuvable'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Événement supprimé avec succès']);
    }

    // Méthode pour supprimer un organisateur avec ses événements
    public function deleteOrganizer($id)
    {
        $organizer = User::find($id);

        if (!$organizer) {
            return response()->json(['error' => 'Organisateur introuvable'], 404);
        }

        if ($organizer->role !== 'organisateur') {
            return response()->json(['error' => 'Cet utilisateur n\'est pas un organisateur'], 400);
        }

        // Supprimer tous les événements créés par cet organisateur
        $organizer->events()->delete();
        $organizer->delete();

        return response()->json(['message' => 'Organisateur et ses événements supprimés avec succès']);
    }


    // Méthode pour lister tous les organisateurs

    public function listOrganizers()
{
    
    $organizers = User::where('role', 'organisateur')->get();

    return response()->json([
        'success' => true,
        'data' => $organizers
    ]);
}

}

