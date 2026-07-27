<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Afficher la liste des événements
     */
    public function index(Request $request)
    {
        try {
            $query = Event::with('village');

            if ($request->has('village_id')) {
                $query->where('village_id', $request->village_id);
            }

            $events = $query->get();

            return response()->json([
                'success' => true,
                'data' => $events
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur index événements : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
        }
    }

    /**
     * Créer un nouvel événement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:Festival,Cérémonie,Marché,Autre',
            'description' => 'nullable|string',
            'village_id' => 'required|integer|exists:villages,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|max:2048', // Validation comme chef_image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $event = new Event();
            $event->name = $request->name;
            $event->type = $request->type;
            $event->description = $request->description;
            $event->village_id = $request->village_id;
            $event->start_date = $request->start_date;
            $event->end_date = $request->end_date;

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('events', 'public');
                $event->image = $path; // Stocke le chemin relatif
            }

            $event->save();
            $event->load('village');

            return response()->json([
                'success' => true,
                'data' => $event,
                'message' => 'Événement créé avec succès'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur store événement : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création'
            ], 500);
        }
    }

    /**
     * Afficher un événement spécifique
     */
    public function show($id)
    {
        try {
            $event = Event::with('village')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $event
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur show événement : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Événement non trouvé'
            ], 404);
        }
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:Festival,Cérémonie,Marché,Autre',
            'description' => 'nullable|string',
            'village_id' => 'required|integer|exists:villages,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $event = Event::findOrFail($id);
            $event->name = $request->name;
            $event->type = $request->type;
            $event->description = $request->description;
            $event->village_id = $request->village_id;
            $event->start_date = $request->start_date;
            $event->end_date = $request->end_date;

            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                if ($event->image) {
                    CloudinaryHelper::delete($event->image);
                }
                $path = $request->file('image')->store('events', 'public');
                $event->image = $path;
            }

            $event->save();
            $event->load('village');

            return response()->json([
                'success' => true,
                'data' => $event,
                'message' => 'Événement mis à jour avec succès'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur update événement : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    /**
     * Supprimer un événement
     */
    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);

            // Supprimer l'image associée
            if ($event->image) {
                CloudinaryHelper::delete($event->image);
            }

            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Événement supprimé avec succès'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur destroy événement : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }
}

