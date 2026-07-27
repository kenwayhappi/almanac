<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Personality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PersonalityController extends Controller
{
    public function index(Request $request)
    {
        $villageId = $request->query('village_id');
        $query = Personality::query();

        if ($villageId) {
            $query->where('village_id', $villageId);
        }

        $personalities = $query->get()->map(function ($personality) {
            // Ajouter l'URL complète pour l'image
            if ($personality->image) {
                $personality->image = url('storage/' . $personality->image);
            }
            return $personality;
        });

        return response()->json(['data' => $personalities], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'statut' => 'required|string|max:100',
            'contact' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'village_id' => 'required|integer|exists:villages,id',
            'has_paid' => 'required|boolean',
            'image' => 'nullable|image|max:2048', // Validation pour fichier image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Échec de la validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only(['name', 'statut', 'contact', 'description', 'village_id', 'has_paid']);

            // Gérer l'upload de l'image
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('personalities', 'public');
                $data['image'] = $imagePath;
            }

            $personality = Personality::create($data);

            // Ajouter l'URL complète pour l'image dans la réponse
            if ($personality->image) {
                $personality->image = url('storage/' . $personality->image);
            }

            return response()->json([
                'data' => $personality,
                'message' => 'Personnalité créée avec succès'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la personnalité', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Erreur interne lors de la création de la personnalité',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $personality = Personality::findOrFail($id);
            if ($personality->image) {
                $personality->image = url('storage/' . $personality->image);
            }
            return response()->json(['data' => $personality], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Personnalité non trouvée',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'statut' => 'required|string|max:100',
            'contact' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'village_id' => 'required|integer|exists:villages,id',
            'has_paid' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            Log::info('Échec de la validation API', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Échec de la validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $personality = Personality::findOrFail($id);
            $data = $request->only(['name', 'statut', 'contact', 'description', 'village_id', 'has_paid']);

            // Gérer la mise à jour de l'image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                CloudinaryHelper::delete($personality->image);
                $imagePath = $request->file('image')->store('personalities', 'public');
                $data['image'] = $imagePath;
            }

            $personality->update($data);

            // Ajouter l'URL complète pour l'image dans la réponse
            $personality->image = $personality->image ? url('storage/' . $personality->image) : null;

            return response()->json([
                'data' => $personality,
                'message' => 'Personnalité mise à jour avec succès'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la personnalité', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Erreur interne lors de la mise à jour de la personnalité',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $personality = Personality::findOrFail($id);
            // Supprimer l'image associée
            CloudinaryHelper::delete($personality->image);
            $personality->delete();

            return response()->json([
                'message' => 'Personnalité supprimée avec succès'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la personnalité', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erreur interne lors de la suppression de la personnalité',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
