<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\AdministrativeDivision;
use App\Models\AdministrativeDivisionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::with([
            'administrativeDivisionTypes' => fn($query) => $query->orderBy('level', 'asc'),
            'administrativeDivisions' => fn($query) => $query->with('children')
        ])->get();

        return response()->json(['success' => true, 'data' => $countries]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|max:5|unique:countries,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:5|unique:countries,code',
            'division_types' => 'nullable|array',
            'division_types.*.name' => 'required|string|max:100',
            'division_types.*.level' => 'required|integer|min:1',
            'divisions' => 'nullable|array',
            'divisions.*.name' => 'required|string|max:100',
            'divisions.*.type_id' => 'nullable|integer',
            'divisions.*.type_name' => 'nullable|string',
            'divisions.*.temp_id' => 'nullable|string',
            'divisions.*.temp_parent_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $country = Country::create([
                'id' => $request->id,
                'name' => $request->name,
                'code' => $request->code,
            ]);

            $typeMap = [];
            if ($request->has('division_types')) {
                foreach ($request->division_types as $typeData) {
                    if (AdministrativeDivisionType::where('country_id', $country->id)->where('level', $typeData['level'])->exists()) {
                        throw new \Exception("Level {$typeData['level']} already used for this country.");
                    }
                    $type = AdministrativeDivisionType::create([
                        'name' => $typeData['name'],
                        'level' => $typeData['level'],
                        'country_id' => $country->id,
                    ]);
                    $typeMap[$typeData['name']] = $type->id;
                }
            }

            $divisionMap = [];
            if ($request->has('divisions')) {
                foreach ($request->divisions as $division) {
                    $divisionData = [
                        'name' => $division['name'],
                        'country_id' => $country->id,
                    ];

                    if (isset($division['type_id'])) {
                        $divisionData['type_id'] = $division['type_id'];
                    } elseif (isset($division['type_name']) && isset($typeMap[$division['type_name']])) {
                        $divisionData['type_id'] = $typeMap[$division['type_name']];
                    } else {
                        throw new \Exception("Division type '{$division['type_name']}' not found.");
                    }

                    if (isset($division['temp_parent_id'])) {
                        if (isset($divisionMap[$division['temp_parent_id']])) {
                            $divisionData['parent_id'] = $divisionMap[$division['temp_parent_id']];
                        } else {
                            $parent = AdministrativeDivision::where('name', $division['temp_parent_id'])
                                ->where('country_id', $country->id)
                                ->first();
                            if ($parent) {
                                $divisionData['parent_id'] = $parent->id;
                            } else {
                                throw new \Exception("Parent division '{$division['temp_parent_id']}' not found.");
                            }
                        }
                    }

                    $newDivision = AdministrativeDivision::create($divisionData);
                    if (isset($division['temp_id'])) {
                        $divisionMap[$division['temp_id']] = $newDivision->id;
                    }
                }
            }

            DB::commit();
            $country->load(['administrativeDivisionTypes', 'administrativeDivisions']);
            return response()->json(['success' => true, 'data' => $country], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $country = Country::with(['administrativeDivisionTypes', 'administrativeDivisions'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $country->id,
                    'name' => $country->name,
                    'code' => $country->code,
                    'administrative_division_types' => $country->administrativeDivisionTypes->map(function ($type) {
                        return [
                            'id' => $type->id,
                            'name' => $type->name,
                            'level' => $type->level,
                        ];
                    }),
                    'administrative_divisions' => $country->administrativeDivisions->map(function ($division) {
                        return [
                            'id' => $division->id,
                            'name' => $division->name,
                            'type_id' => $division->type_id,
                            'parent_id' => $division->parent_id,
                        ];
                    }),
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du pays : ', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Pays non trouvé',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id' => 'string|max:5|unique:countries,id,' . $country->id,
            'name' => 'string|max:100',
            'code' => 'string|max:5|unique:countries,code,' . $country->id,
            'division_types' => 'nullable|array',
            'division_types.*.name' => 'required|string|max:100',
            'division_types.*.level' => 'required|integer|min:1',
            'divisions' => 'nullable|array',
            'divisions.*.name' => 'required|string|max:100',
            'divisions.*.type_id' => 'nullable|integer',
            'divisions.*.type_name' => 'nullable|string',
            'divisions.*.temp_id' => 'nullable|string',
            'divisions.*.temp_parent_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $country->update([
                'id' => $request->input('id', $country->id),
                'name' => $request->input('name', $country->name),
                'code' => $request->input('code', $country->code),
            ]);

            $typeMap = [];
            if ($request->has('division_types')) {
                $country->administrativeDivisionTypes()->delete();
                foreach ($request->division_types as $typeData) {
                    $type = $country->administrativeDivisionTypes()->create([
                        'name' => $typeData['name'],
                        'level' => $typeData['level'],
                    ]);
                    $typeMap[$typeData['name']] = $type->id;
                }
            }

            if ($request->has('divisions')) {
                $divisionMap = [];
                foreach ($request->divisions as $division) {
                    $divisionData = [
                        'name' => $division['name'],
                        'country_id' => $country->id,
                    ];

                    if (isset($division['type_id'])) {
                        $divisionData['type_id'] = $division['type_id'];
                    } elseif (isset($division['type_name']) && isset($typeMap[$division['type_name']])) {
                        $divisionData['type_id'] = $typeMap[$division['type_name']];
                    } else {
                        throw new \Exception("Division type '{$division['type_name']}' not found.");
                    }

                    if (isset($division['temp_parent_id'])) {
                        if (isset($divisionMap[$division['temp_parent_id']])) {
                            $divisionData['parent_id'] = $divisionMap[$division['temp_parent_id']];
                        } else {
                            $parent = AdministrativeDivision::where('name', $division['temp_parent_id'])
                                ->where('country_id', $country->id)
                                ->first();
                            if ($parent) {
                                $divisionData['parent_id'] = $parent->id;
                            } else {
                                throw new \Exception("Parent division '{$division['temp_parent_id']}' not found.");
                            }
                        }
                    }

                    $newDivision = AdministrativeDivision::updateOrCreate(
                        ['name' => $division['name'], 'country_id' => $country->id],
                        $divisionData
                    );
                    if (isset($division['temp_id'])) {
                        $divisionMap[$division['temp_id']] = $newDivision->id;
                    }
                }
                $newDivisionNames = collect($request->divisions)->pluck('name')->toArray();
                AdministrativeDivision::where('country_id', $country->id)
                    ->whereNotIn('name', $newDivisionNames)
                    ->delete();
            }

            DB::commit();
            $country->load(['administrativeDivisionTypes', 'administrativeDivisions']);
            return response()->json(['success' => true, 'data' => $country]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $country = Country::findOrFail($id);

        DB::beginTransaction();
        try {
            $country->administrativeDivisionTypes()->delete();
            $country->administrativeDivisions()->delete();
            $country->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Country deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function hierarchy($id)
    {
        $country = Country::with([
            'administrativeDivisionTypes' => fn($query) => $query->orderBy('level', 'asc'),
            'administrativeDivisions' => fn($query) => $query->whereNull('parent_id')
                ->with([
                    'children',
                    'villageGroups' // Load villageGroups through administrativeDivisions
                ]),
        ])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $country]);
    }
}
