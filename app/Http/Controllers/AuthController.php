<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

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
        // Validation des données entrantes
        $request->validate([
            'username' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|max:255|unique:users,email,' . auth()->id(),
            'password' => 'sometimes|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();

        // Mise à jour des informations du profil
        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        // Gestion de la photo de profil
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancienne photo s'il y en a une
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }
            // Sauvegarder la nouvelle photo
            $imagePath = $request->file('avatar')->store('avatar', 'public');
            $user->avatar = $imagePath;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'user' => $user
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour du profil',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
