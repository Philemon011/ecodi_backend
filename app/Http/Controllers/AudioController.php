<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Cours;
use Illuminate\Support\Facades\Validator;


use Illuminate\Http\Request;

class AudioController extends Controller
{
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'cours_id' => 'required|exists:cours,id', // Le cours doit exister
            'fichier' => 'required|file|mimes:mp3,wav,jpeg,png,jpg|max:10240', // Taille max : 10 Mo
        ]);

        // Vérifier si la validation échoue
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Récupérer les données validées
        $validatedData = $validator->validated();

        // Récupérer le cours
        $cours = Cours::findOrFail($validatedData['cours_id']);

        $audioCount = $cours->audios()->count();

        // Vérifier si le cours a déjà 5 audios
        // if ($audioCount >= 5) {
        //     return response()->json(['message' => 'Un cours ne peut contenir que 5 audios au maximum.'], 422);
        // }

        // Définir le nom de l'audio par défaut
        $audioName = "Audio " . ($audioCount + 1);

        // Enregistrer le fichier audio
        $path = $request->file('fichier')->store('cours_audios', 'public');

        // Créer l'audio
        $audio = Audio::create([
            'cours_id' => $validatedData['cours_id'],
            'nom' => $audioName,
            'fichier' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => "laudio a été bien ajouté !",
            'audio' => $audio
        ], 201);
    }
}
