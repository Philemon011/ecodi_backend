<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;
use App\Models\Cours;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;



use Illuminate\Http\Request;

class CoursController extends Controller
{
    public function index()
    {
        //get posts
        $cours = Cours::with('audios')->get();
        // $cours = DB::table('cours')
        //     ->select('cours.*')
        //     ->orderBy('cours.created_at', 'desc')
        //     ->get();

        //return collection of signalements as a resource

        return response([
            'success' => true,
            'data' => $cours,
            'message' => "Liste des cours",
        ], 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Limite de 2 Mo
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cheminImage = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension(); // Récupérer l'extension du fichier
            $nomFichier = Str::slug($request->titre) . '.' . $extension; // Utiliser le titre comme nom de fichier
            $cheminImage = $image->storeAs('images_cours', $nomFichier, 'public');
        }

        $cours = Cours::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'image' => $cheminImage,
        ]);

        return response([
            'success' => true,
            'message' => "Le cours a été bien enregistré !",
            'cours' => $cours,
        ], 201);
    }


    // NB si la methode UPDATE ne marche pas dans postman (formdata), il faut utiliser POST en ajoutant la clé: "_method" => "PUT"
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Limite de 2 Mo
        ]);




        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Récupérer le cours à mettre à jour
        $cours = Cours::find($id);
        if (!$cours) {
            return response()->json(['error' => 'Cours non trouvé'], 404);
        }

        // Mise à jour des données
        $cheminImage = $cours->image; // Conserver l'ancienne image par défaut

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($cours->image && Storage::disk('public')->exists($cours->image)) {
                Storage::disk('public')->delete($cours->image);
            }

            // Sauvegarder la nouvelle image
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $nomFichier = Str::slug($request->titre) . '.' . $extension; // Renommer avec le titre
            $cheminImage = $image->storeAs('images_cours', $nomFichier, 'public');
        }

        // Mettre à jour les champs du cours
        $cours->update([
            'titre' => $request->titre,
            'description' => $request->description ? $request->description : $cours->description,
            'image' => $cheminImage,
        ]);

        return response([
            'success' => true,
            'message' => "Le cours a été bien mis à jour !",
            'cours' => $cours,
        ], 200);
    }

    public function destroy($id)
    {
        // Récupérer le cours à supprimer
        $cours = Cours::find($id);
        if (!$cours) {
            return response()->json(['error' => 'Cours non trouvé'], 404);
        }

        // Supprimer l'image associée si elle existe
        if ($cours->image && Storage::disk('public')->exists($cours->image)) {
            Storage::disk('public')->delete($cours->image);
        }

        // Supprimer le cours de la base de données
        $cours->delete();

        return response()->json([
            'success' => true,
            'message' => "Le cours a été supprimé avec succès !",
        ], 200);
    }
}
