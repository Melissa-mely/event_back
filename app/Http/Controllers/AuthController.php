<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:50',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,organisateur,participant',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            // Gestion de l'upload de l'image si fournie
        if ($request->hasFile('avatar')) {
            $imagePath = $request->file('avatar')->store('avatar', 'public');
        } else {
            $imagePath = null;
        }
    
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'avatar' => $imagePath
                
            ]);
    
    
            return response()->json([
                'message' => 'Utilisateur créé avec succès',
                'user' => $user
            ], 201);
    
        } catch (Exception $e) {
    
            return response()->json([
                'message' => 'Erreur lors de l\'inscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
{
    try {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role
            ]
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la connexion',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function logout(Request $request)
{
    $request->user()->tokens()->delete();

    return response()->json([
        'message' => 'Déconnexion réussie'
    ]);
}

public function updateProfile(Request $request)
{
    try {
        // Récupérer l'utilisateur authentifié
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé',
            ], 404);
        }

        // Log pour voir les données envoyées
     
        // Validation des champs
        $validatedData = $request->validate([
            'username' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        Log::info('Données reçues :', $request->all());

        // Mise à jour des champs textuels
        if ($request->has('username')) {
            $user->username = $request->input('username');
        }

        if ($request->has('email')) {
            $user->email = $request->input('email');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Gestion de l'avatar
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancienne photo si elle existe
            Log::info('Fichier avatar reçu :', $request->file('avatar'));
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }

            // Sauvegarder la nouvelle photo
            $imagePath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $imagePath;
        }

        // Enregistrer les modifications
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'user' => $user,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour du profil',
            'error' => $e->getMessage(),
        ], 500);
    }
}





}
