<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeDivisionType;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdministrativeDivisionTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = AdministrativeDivisionType::orderBy('level', 'asc');
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'level' => 'required|integer|min:1|max:5',
            'country_id' => 'required|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if (AdministrativeDivisionType::where('country_id', $request->country_id)->where('level', $request->level)->exists()) {
            return response()->json(['success' => false, 'errors' => ['level' => ['Level already used for this country']]], 422);
        }

        $type = AdministrativeDivisionType::create($request->all());
        return response()->json(['success' => true, 'data' => $type], 201);
    }

    public function show($id)
    {
        $type = AdministrativeDivisionType::with(['country', 'administrativeDivisions'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $type = AdministrativeDivisionType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:100',
            'level' => 'integer|min:1|max:5',
            'country_id' => 'exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->has('level') && $request->level != $type->level) {
            if (AdministrativeDivisionType::where('country_id', $type->country_id)->where('level', $request->level)->exists()) {
                return response()->json(['success' => false, 'errors' => ['level' => ['Level already used for this country']]], 422);
            }
        }

        $type->update($request->all());
        return response()->json(['success' => true, 'data' => $type]);
    }

    public function destroy($id)
    {
        $type = AdministrativeDivisionType::findOrFail($id);

        if ($type->administrativeDivisions()->exists()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete type with associated divisions'], 422);
        }

        $type->delete();
        return response()->json(['success' => true, 'message' => 'Type deleted successfully']);
    }

    public function getTypesByCountry($countryId)
    {
        Country::findOrFail($countryId);
        $types = AdministrativeDivisionType::where('country_id', $countryId)->orderBy('level', 'asc')->get();
        return response()->json(['success' => true, 'data' => $types]);
    }
}