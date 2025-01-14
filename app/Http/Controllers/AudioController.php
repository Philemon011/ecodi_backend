<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Cours;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


use Illuminate\Http\Request;

class AudioController extends Controller
{
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'cours_id' => 'required|exists:cours,id', // Le cours doit exister
            'nom'=>'required|string|max:255',
            'fichier' => 'required|file|mimes:mp3,wav,jpeg,png,jpg|max:10240', // Taille max : 10 Mo
        ]);

        // Vérifier si la validation échoue
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }


        // Enregistrer le fichier audio
        $path = $request->file('fichier')->store('cours_audios', 'public');

        // Créer l'audio
        $audio = Audio::create([
            'cours_id' => $request->cours_id,
            'nom' => $request->nom,
            'fichier' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => "l'audio a été bien ajouté !",
            'audio' => $audio
        ], 201);
    }

    public function update(Request $request, $id)
{
    // Validation des données
    $validator = Validator::make($request->all(), [
        'cours_id' => 'required|exists:cours,id', // Le cours doit exister
        'nom' => 'required|string|max:255',
        'fichier' => 'nullable|file|mimes:mp3,wav,jpeg,png,jpg|max:10240', // Taille max : 10 Mo
    ]);

    // Vérifier si la validation échoue
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    // Récupérer l'audio à mettre à jour
    $audio = Audio::find($id);
    if (!$audio) {
        return response()->json(['error' => 'Audio non trouvé'], 404);
    }

    // Conserver le chemin actuel du fichier
    $path = $audio->fichier;

    // Si un nouveau fichier est envoyé, remplacez l'ancien
    if ($request->hasFile('fichier')) {
        // Supprimer l'ancien fichier s'il existe
        if ($audio->fichier && Storage::disk('public')->exists($audio->fichier)) {
            Storage::disk('public')->delete($audio->fichier);
        }

        // Enregistrer le nouveau fichier
        $path = $request->file('fichier')->store('cours_audios', 'public');
    }

    // Mise à jour des données
    $audio->update([
        'cours_id' => $request->cours_id,
        'nom' => $request->nom,
        'fichier' => $path,
    ]);

    return response()->json([
        'success' => true,
        'message' => "L'audio a été bien mis à jour !",
        'audio' => $audio,
    ], 200);
}





    public function destroy($id)
    {
        // Récupérer le cours à supprimer
        $audio = Audio::find($id);
        if (!$audio) {
            return response()->json(['error' => 'audio non trouvé'], 404);
        }

        // Supprimer l'image associée si elle existe
        if ($audio->fichier && Storage::disk('public')->exists($audio->fichier)) {
            Storage::disk('public')->delete($audio->fichier);
        }

        // Supprimer le cours de la base de données
        $audio->delete();

        return response()->json([
            'success' => true,
            'message' => "L'audio a été supprimé avec succès !",
        ], 200);
    }
}
