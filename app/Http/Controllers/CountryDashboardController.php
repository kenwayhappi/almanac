<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\AdministrativeDivision;
use App\Models\AdministrativeDivisionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CountryDashboardController extends Controller
{
    public function index()
    {
        $countries = Country::with([
            'administrativeDivisionTypes' => fn($query) => $query->orderBy('level', 'asc'),
        ])
        ->withCount('administrativeDivisions')
        ->paginate(15);

        return view('dashboard.pays.index', compact('countries'));
    }

    public function create()
    {
        return view('dashboard.pays.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|max:5|unique:countries,id',
            'nom' => 'required|string|max:100',
            'code' => 'required|string|max:5|unique:countries,code',
            'divisions_types' => 'required|array|min:1',
            'divisions_types.*' => 'required|string|max:100',
            'divisions' => 'nullable|array',
            'divisions.*.name' => 'required|string|max:100',
            'divisions.*.type_level' => 'required|integer|min:1',
            'divisions.*.parent_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $country = Country::create([
                'id' => $request->id,
                'name' => $request->nom,
                'code' => $request->code,
            ]);

            $typeMap = [];
            foreach ($request->divisions_types as $index => $name) {
                $type = AdministrativeDivisionType::create([
                    'name' => $name,
                    'level' => $index + 1,
                    'country_id' => $country->id,
                ]);
                $typeMap[$index + 1] = $type->id;
            }

            if ($request->has('divisions') && is_array($request->divisions)) {
                $divisionMap = [];
                foreach ($request->divisions as $division) {
                    $parentName = !empty($division['parent_name']) ? trim($division['parent_name']) : null;
                    $parentId = $parentName && isset($divisionMap[$parentName]) ? $divisionMap[$parentName] : null;

                    $newDivision = AdministrativeDivision::create([
                        'name' => trim($division['name']),
                        'country_id' => $country->id,
                        'type_id' => $typeMap[$division['type_level']] ?? null,
                        'parent_id' => $parentId,
                    ]);

                    $divisionMap[trim($division['name'])] = $newDivision->id;
                }
            }

            DB::commit();
            return redirect()->route('dashboard.pays.index')->with('success', 'Pays créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $country = Country::with([
            'administrativeDivisionTypes' => fn($query) => $query->orderBy('level', 'asc'),
            'administrativeDivisions' => fn($query) => $query->whereNull('parent_id')->with('children')
        ])->findOrFail($id);
        return view('dashboard.pays.show', compact('country'));
    }

    public function edit($id)
    {
        $country = Country::with([
            'administrativeDivisionTypes' => fn($query) => $query->orderBy('level', 'asc'),
            'administrativeDivisions' => fn($query) => $query->whereNull('parent_id')->with('children')
        ])->findOrFail($id);
        return view('dashboard.pays.edit', compact('country'));
    }

    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id' => 'required|string|max:5|unique:countries,id,' . $country->id,
            'nom' => 'required|string|max:100',
            'code' => 'required|string|max:5|unique:countries,code,' . $country->id,
            'divisions_types' => 'required|array|min:1',
            'divisions_types.*' => 'required|string|max:100',
            'divisions' => 'nullable|array',
            'divisions.*.name' => 'required|string|max:100',
            'divisions.*.type_level' => 'required|integer|min:1',
            'divisions.*.parent_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $country->update([
                'id' => $request->id,
                'name' => $request->nom,
                'code' => $request->code,
            ]);

            $country->administrativeDivisionTypes()->delete();
            $typeMap = [];
            foreach ($request->divisions_types as $index => $name) {
                $type = AdministrativeDivisionType::create([
                    'name' => $name,
                    'level' => $index + 1,
                    'country_id' => $country->id,
                ]);
                $typeMap[$index + 1] = $type->id;
            }

            if ($request->has('divisions') && is_array($request->divisions)) {
                $country->administrativeDivisions()->delete();
                $divisionMap = [];
                foreach ($request->divisions as $division) {
                    $parentName = !empty($division['parent_name']) ? trim($division['parent_name']) : null;
                    $parentId = $parentName && isset($divisionMap[$parentName]) ? $divisionMap[$parentName] : null;

                    $newDivision = AdministrativeDivision::create([
                        'name' => trim($division['name']),
                        'country_id' => $country->id,
                        'type_id' => $typeMap[$division['type_level']] ?? null,
                        'parent_id' => $parentId,
                    ]);

                    $divisionMap[trim($division['name'])] = $newDivision->id;
                }
            }

            DB::commit();
            return redirect()->route('dashboard.pays.index')->with('success', 'Pays mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
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
            return redirect()->route('dashboard.pays.index')->with('success', 'Pays supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
