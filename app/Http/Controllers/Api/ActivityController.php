<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CloudinaryHelper;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('village');

        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        $activites = $query->get();

        return response()->json([
            'success' => true,
            'data' => $activites
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:Festival,Atelier,Cérémonie,Autre',
            'description' => 'nullable|string',
            'village_id' => 'required|exists:villages,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Accepte un fichier image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'type', 'description', 'village_id']);

        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $publicId = CloudinaryHelper::upload($request->file('image'), 'activites/images');
            $data['image'] = $publicId;
        }

        $activity = Activity::create($data);

        return response()->json([
            'success' => true,
            'data' => $activity->load('village')
        ], 201);
    }

    public function show($id)
    {
        $activity = Activity::with('village')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $activity
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:Festival,Atelier,Cérémonie,Autre',
            'description' => 'nullable|string',
            'village_id' => 'required|exists:villages,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $activity = Activity::findOrFail($id);
        $data = $request->only(['name', 'type', 'description', 'village_id']);

        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            CloudinaryHelper::delete($activity->image);
            $publicId = CloudinaryHelper::upload($request->file('image'), 'activites/images');
            $data['image'] = $publicId;
        }

        $activity->update($data);

        return response()->json([
            'success' => true,
            'data' => $activity->load('village')
        ]);
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);

        CloudinaryHelper::delete($activity->image);

        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Activité supprimée avec succès'
        ]);
    }
}