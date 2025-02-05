<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;



class IndexController extends Controller
{
      // ------------------- recuperation de tous les evenements  ---------------------------------------


      public function index()
      {
       // Récupérer les événements triés par date de création (du plus récent au plus ancien)
    $events = Event::with(['categories', 'organizer'])
    ->orderBy('date', 'desc') // Trier par création récente
    ->take(5) // Prendre les 5 derniers événements créés
    ->get();

return response()->json([
    'success' => true,
    'data' => $events
]);
      }
       
       // ------------------- voir les details d'un evenément ---------------------------------------
      public function show($id)
      {
          // Récupérer l'événement avec ses catégories et l'organisateur
          $event = Event::with(['categories', 'organizer'])->find($id);
      
          // Vérifier si l'événement existe
          if (!$event) {
              return response()->json(['error' => 'Événement non trouvé'], 404);
          }
      
          return response()->json([
              'success' => true,
              'data' => $event
          ]);
      }
      
       // ------------------- rechercher un event par mot-clé ---------------------------------------
      public function search(Request $request)
      {
      
          $query = $request->input('q'); // Récupère le mot-clé depuis la requête
      
          if (!$query) {
              return response()->json(['error' => 'Veuillez fournir un mot-clé'], 400);
          }
      
          // Recherche des événements dont le titre ou la description contient le mot-clé
          $events = Event::where('title', 'LIKE', "%$query%")
                          ->orWhere('description', 'LIKE', "%$query%")
                          ->with(['categories', 'organizer'])
                          ->get();
      
          return response()->json([
              'success' => true,
              'data' => $events
          ]);
      }
      
          // ------------------- filtrer les evenements par catégorie ---------------------------------------
      public function filterByCategory($categoryId)
      {
          // Vérifier si la catégorie existe
          $category = Category::find($categoryId);
          if (!$category) {
              return response()->json(['error' => 'Catégorie non trouvée'], 404);
          }
      
          // Récupérer les événements de cette catégorie
          $events = Event::whereHas('categories', function ($query) use ($categoryId) {
              $query->where('categories.id', $categoryId);
          })->with(['categories', 'organizer'])->get();
      
          return response()->json([
              'success' => true,
              'data' => $events
          ]);
      }
      // ------------------- voir les evenements a venir ---------------------------------------
public function getUpcomingEvents()
{
    // Récupérer la date et l'heure actuelles
    $today = Carbon::now();
    

    // Récupérer les événements futurs
    $events = Event::where('date', '>=', $today)
        ->orderBy('date', 'asc') // Trier par date croissante
        ->with(['categories', 'organizer']) // Charger les relations
        ->get();

    return response()->json([
        'success' => true,
        'data' => $events
    ]);
}

}
