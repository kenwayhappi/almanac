<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeDivision;
use App\Models\AdministrativeDivisionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdministrativeDivisionController extends Controller
{
    public function index(Request $request)
    {
        $query = AdministrativeDivision::with(['type', 'parent', 'country']);
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        if ($request->has('type_id')) {
            $query->where('type_id', $request->type_id);
        }
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id == '0' ? null : $request->parent_id);
        }
        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'type_id' => 'required|exists:administrative_division_types,id',
            'parent_id' => 'nullable|exists:administrative_divisions,id',
            'country_id' => 'required|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $type = AdministrativeDivisionType::findOrFail($request->type_id);
        if ($type->country_id != $request->country_id) {
            return response()->json(['success' => false, 'errors' => ['type_id' => ['Invalid country for this type']]], 422);
        }

        if ($request->parent_id) {
            $parent = AdministrativeDivision::findOrFail($request->parent_id);
            if ($parent->country_id != $request->country_id) {
                return response()->json(['success' => false, 'errors' => ['parent_id' => ['Invalid country for parent']]], 422);
            }
            if ($parent->type->level >= $type->level) {
                return response()->json(['success' => false, 'errors' => ['parent_id' => ['Parent level must be higher']]], 422);
            }
        } else {
            if ($type->level != 1 && AdministrativeDivisionType::where('country_id', $request->country_id)->where('level', '<', $type->level)->exists()) {
                return response()->json(['success' => false, 'errors' => ['parent_id' => ['Parent required for this level']]], 422);
            }
        }

        $division = AdministrativeDivision::create($request->all());
        $division->load(['type', 'parent', 'country']);
        return response()->json(['success' => true, 'data' => $division], 201);
    }

    public function show($id)
    {
        $division = AdministrativeDivision::with(['type', 'parent', 'children', 'country'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $division]);
    }

    public function update(Request $request, $id)
    {
        $division = AdministrativeDivision::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:100',
            'type_id' => 'exists:administrative_division_types,id',
            'parent_id' => 'nullable|exists:administrative_divisions,id',
            'country_id' => 'exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->hasAny(['type_id', 'parent_id', 'country_id'])) {
            $typeId = $request->type_id ?? $division->type_id;
            $parentId = $request->has('parent_id') ? $request->parent_id : $division->parent_id;
            $countryId = $request->country_id ?? $division->country_id;

            $type = AdministrativeDivisionType::findOrFail($typeId);
            if ($type->country_id != $countryId) {
                return response()->json(['success' => false, 'errors' => ['type_id' => ['Invalid country for this type']]], 422);
            }

            if ($parentId) {
                $parent = AdministrativeDivision::findOrFail($parentId);
                if ($parent->country_id != $countryId) {
                    return response()->json(['success' => false, 'errors' => ['parent_id' => ['Invalid country for parent']]], 422);
                }
                if ($parent->type->level >= $type->level) {
                    return response()->json(['success' => false, 'errors' => ['parent_id' => ['Parent level must be higher']]], 422);
                }
                if ($parentId == $id || $division->descendants()->pluck('id')->contains($parentId)) {
                    return response()->json(['success' => false, 'errors' => ['parent_id' => ['Cannot set child as parent']]], 422);
                }
            } elseif ($request->has('parent_id') && $request->parent_id === null) {
                if ($type->level != 1 && AdministrativeDivisionType::where('country_id', $countryId)->where('level', '<', $type->level)->exists()) {
                    return response()->json(['success' => false, 'errors' => ['parent_id' => ['Parent required for this level']]], 422);
                }
            }
        }

        $division->update($request->all());
        $division->load(['type', 'parent', 'country']);
        return response()->json(['success' => true, 'data' => $division]);
    }

    public function destroy($id)
    {
        $division = AdministrativeDivision::findOrFail($id);

        if ($division->children()->exists()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete division with children'], 422);
        }
        if ($division->villages()->exists()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete division with villages'], 422);
        }

        $division->delete();
        return response()->json(['success' => true, 'message' => 'Division deleted successfully']);
    }

    public function getDivisionsByCountry($countryId)
    {
        $divisions = AdministrativeDivision::where('country_id', $countryId)
            ->with(['type', 'parent'])
            ->get();
        return response()->json(['success' => true, 'data' => $divisions]);
    }

    public function getChildren($id)
    {
        $division = AdministrativeDivision::findOrFail($id);
        $children = $division->children()->with(['type'])->get();
        return response()->json(['success' => true, 'data' => $children]);
    }

    public function getVillages($id)
    {
        $division = AdministrativeDivision::findOrFail($id);
        $villages = $division->getAllVillages();
        return response()->json(['success' => true, 'data' => $villages]);
    }
}
