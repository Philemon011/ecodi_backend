<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Cours;
use Illuminate\Support\Facades\DB;

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
            $cheminImage = $request->file('image')->store('images_cours', 'public');
        }




        $cours = Cours::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'image' => $cheminImage,
        ]);

        return response([
            'success' => true,
            'message' => "le cours a été bien enrégistré !",
            'cours'=>$cours,
        ], 201);
    }
}
