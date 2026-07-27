<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillageGroup;
use App\Models\AdministrativeDivision;
use App\Models\country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VillageGroupController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = VillageGroup::with('parent');

            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            }

            $groups = $query->get();

            return response()->json([
                'success' => true,
                'data' => $groups
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur index village groups : ', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $group = VillageGroup::with('parent')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $group
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur show village group : ', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Groupement non trouvé'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'chef_groupement' => 'nullable|string|max:100',
            'histoire' => 'nullable|string',
            'parent_id' => 'required|exists:administrative_divisions,id',
            'chef_image' => 'nullable|image|max:2048',
            'image' => 'nullable|image|max:2048', // Une seule image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $group = new VillageGroup();
            $group->name = $request->name;
            $group->description = $request->description;
            $group->chef_groupement = $request->chef_groupement;
            $group->histoire = $request->histoire;
            $group->parent_id = $request->parent_id;

            if ($request->hasFile('chef_image')) {
                $path = $request->file('chef_image')->store('groupements/chefs', 'public');
                $group->chef_image = $path;
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('groupements/images', 'public');
                $group->image = $path;
            }

            $group->save();
            $group->load('parent');

            return response()->json([
                'success' => true,
                'data' => $group,
                'message' => 'Groupement créé avec succès'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur store village group : ', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création : ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Début update village group : ', ['id' => $id, 'input' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'chef_groupement' => 'nullable|string|max:100',
            'histoire' => 'nullable|string',
            'parent_id' => 'required|exists:administrative_divisions,id',
            'chef_image' => 'nullable|image|max:2048',
            'image' => 'nullable|image|max:2048', // Une seule image
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée pour update village group : ', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $group = VillageGroup::findOrFail($id);
            $group->name = $request->name;
            $group->description = $request->description;
            $group->chef_groupement = $request->chef_groupement;
            $group->histoire = $request->histoire;
            $group->parent_id = $request->parent_id;

            if ($request->hasFile('chef_image')) {
                CloudinaryHelper::delete($group->chef_image);
                $path = $request->file('chef_image')->store('groupements/chefs', 'public');
                $group->chef_image = $path;
                Log::info('Nouvelle chef_image stockée : ', ['path' => $path]);
            }

            if ($request->hasFile('image')) {
                CloudinaryHelper::delete($group->image);
                $path = $request->file('image')->store('groupements/images', 'public');
                $group->image = $path;
                Log::info('Nouvelle image stockée : ', ['path' => $path]);
            }

            $group->save();
            $group->load('parent');

            Log::info('Groupement mis à jour avec succès : ', ['id' => $id]);

            return response()->json([
                'success' => true,
                'data' => $group,
                'message' => 'Groupement mis à jour avec succès'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur update village group : ', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $group = VillageGroup::findOrFail($id);

            DB::beginTransaction();

            CloudinaryHelper::delete($group->chef_image);

            CloudinaryHelper::delete($group->image);

            $villages = $group->villages;
            foreach ($villages as $village) {
                CloudinaryHelper::delete($village->image);
            }
            $group->villages()->delete();
            $group->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Groupement et villages associés supprimés avec succès'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur destroy village group : ', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }

    public function getGroupsByDivision($divisionId)
    {
        try {
            $division = AdministrativeDivision::findOrFail($divisionId);
            $groups = VillageGroup::where('parent_id', $divisionId)->get();

            return response()->json([
                'success' => true,
                'data' => $groups
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur getGroupsByDivision : ', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
        }
    }

    public function getVillages($id)
    {
        try {
            $group = VillageGroup::findOrFail($id);
            $villages = $group->villages()->with('administrativeDivision')->get();

            return response()->json([
                'success' => true,
                'data' => $villages
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur getVillages : ', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
        }
    }
    public function getGroupsByCountry($countryId)
{
    try {
        // Verify the country exists
        $country = Country::findOrFail($countryId);

        // Fetch village groups where the parent (AdministrativeDivision) belongs to the specified country
        $groups = VillageGroup::whereHas('parent', function ($query) use ($countryId) {
            $query->where('country_id', $countryId);
        })->with('parent')->get();

        return response()->json([
            'success' => true,
            'data' => $groups
        ], 200);
    } catch (\Exception $e) {
        Log::error('Erreur getGroupsByCountry : ', ['message' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur'
        ], 500);
    }
}
}

