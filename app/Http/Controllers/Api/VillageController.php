<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VillageController extends Controller
{
    public function index(Request $request)
    {
        $query = Village::with('villageGroup');
        if ($request->has('village_group_id')) {
            $query->where('village_group_id', $request->village_group_id);
        }
        $villages = $query->get();
        return response()->json(['success' => true, 'data' => $villages]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'histoire' => 'nullable|string',
            'population' => 'nullable|integer',
            'village_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'chief_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'chef_village' => 'nullable|string|max:100',
            'is_village' => 'boolean',
            'village_group_id' => 'required|exists:village_groups,id',
            'current_chief' => 'nullable|string',
            'chief_description' => 'nullable|string',
            'chief_achievements' => 'nullable|string',
            'chief_interventions' => 'nullable|string',
            'village_history' => 'nullable|string',
            'historical_dynasty' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'name', 'description', 'histoire', 'population', 'chef_village',
            'is_village', 'village_group_id', 'current_chief', 'chief_description',
            'chief_achievements', 'chief_interventions', 'village_history', 'historical_dynasty'
        ]);

        if ($request->hasFile('village_image')) {
            $data['village_image'] = $request->file('village_image')->store('villages/images', 'public');
        }
        if ($request->hasFile('chief_image')) {
            $data['chief_image'] = $request->file('chief_image')->store('villages/chiefs', 'public');
        }

        $village = Village::create($data);
        return response()->json(['success' => true, 'data' => $village->load('villageGroup')], 201);
    }

    public function show($id)
    {
        $village = Village::with('villageGroup')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $village]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'histoire' => 'nullable|string',
            'population' => 'nullable|integer',
            'village_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'chief_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'chef_village' => 'nullable|string|max:100',
            'is_village' => 'boolean',
            'village_group_id' => 'required|exists:village_groups,id',
            'current_chief' => 'nullable|string',
            'chief_description' => 'nullable|string',
            'chief_achievements' => 'nullable|string',
            'chief_interventions' => 'nullable|string',
            'village_history' => 'nullable|string',
            'historical_dynasty' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $village = Village::findOrFail($id);
        $data = $request->only([
            'name', 'description', 'histoire', 'population', 'chef_village',
            'is_village', 'village_group_id', 'current_chief', 'chief_description',
            'chief_achievements', 'chief_interventions', 'village_history', 'historical_dynasty'
        ]);

        if ($request->hasFile('village_image')) {
            if ($village->village_image) {
                CloudinaryHelper::delete($village->village_image);
            }
            $data['village_image'] = $request->file('village_image')->store('villages/images', 'public');
        }
        if ($request->hasFile('chief_image')) {
            if ($village->chief_image) {
                CloudinaryHelper::delete($village->chief_image);
            }
            $data['chief_image'] = $request->file('chief_image')->store('villages/chiefs', 'public');
        }

        $village->update($data);
        return response()->json(['success' => true, 'data' => $village->load('villageGroup')]);
    }

    public function destroy($id)
    {
        $village = Village::findOrFail($id);
        if ($village->village_image) {
            CloudinaryHelper::delete($village->village_image);
        }
        if ($village->chief_image) {
            CloudinaryHelper::delete($village->chief_image);
        }
        $village->delete();
        return response()->json(['success' => true, 'message' => 'Village supprimé avec succès']);
    }

    public function getVillagesByDivision($divisionId)
    {
        $villages = Village::whereHas('villageGroup', function ($query) use ($divisionId) {
            $query->where('parent_id', $divisionId);
        })->get();
        return response()->json(['success' => true, 'data' => $villages]);
    }

    public function getVillagesByGroup($groupId)
    {
        $villages = Village::where('village_group_id', $groupId)->get();
        return response()->json(['success' => true, 'data' => $villages]);
    }

    public function search(Request $request)
    {
        $query = Village::query();
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        $villages = $query->get();
        return response()->json(['success' => true, 'data' => $villages]);
    }
}


