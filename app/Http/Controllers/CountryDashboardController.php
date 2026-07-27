<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
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
            'administrativeDivisions' => fn($query) => $query->with(['type', 'parent', 'children.children']),
        ])
        ->withCount('administrativeDivisions')
        ->paginate(15);

        return view('dashboard.pays.index', compact('countries'));
    }

    public function storeDivision(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:100',
            'type_id' => 'required|exists:administrative_division_types,id',
            'parent_id' => 'nullable|exists:administrative_divisions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Veuillez vérifier les informations de la division.');
        }

        AdministrativeDivision::create([
            'country_id' => $request->country_id,
            'name'       => trim($request->name),
            'type_id'    => $request->type_id,
            'parent_id'  => $request->parent_id ?: null,
        ]);

        return redirect()->route('dashboard.pays.index')->with('success', 'Division administrative ajoutée avec succès.');
    }

    public function updateDivision(Request $request, $id)
    {
        $division = AdministrativeDivision::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:100',
            'type_id'   => 'nullable|exists:administrative_division_types,id',
            'parent_id' => 'nullable|exists:administrative_divisions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Veuillez vérifier le nom de la division.');
        }

        $division->update([
            'name'      => trim($request->name),
            'type_id'   => $request->type_id ?? $division->type_id,
            'parent_id' => $request->filled('parent_id') ? $request->parent_id : $division->parent_id,
        ]);

        return redirect()->route('dashboard.pays.index')->with('success', 'Division administrative mise à jour avec succès.');
    }

    public function destroyDivision($id)
    {
        $division = AdministrativeDivision::findOrFail($id);

        try {
            // Transférer ou détacher les sous-divisions filles
            $division->children()->update(['parent_id' => null]);
            $division->delete();
            return redirect()->route('dashboard.pays.index')->with('success', 'Division administrative supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression de la division : ' . $e->getMessage());
        }
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
            // Supprimer tous les groupements rattachés à ce pays via les divisions
            $divisionIds = $country->administrativeDivisions()->pluck('id');
            $groups = \App\Models\VillageGroup::whereIn('parent_id', $divisionIds)->get();

            foreach ($groups as $group) {
                // Images du groupement
                CloudinaryHelper::delete($group->chef_image);
                CloudinaryHelper::delete($group->image);

                // Personnalités administratives
                foreach ($group->personnalitesAdministratives as $pa) {
                    CloudinaryHelper::delete($pa->photo);
                    $pa->delete();
                }

                // Villages du groupement & leurs sous-ressources
                foreach ($group->villages as $village) {
                    CloudinaryHelper::delete($village->village_image);
                    CloudinaryHelper::delete($village->chief_image);

                    foreach ($village->events as $ev) {
                        $ev->contributions()->delete();
                        CloudinaryHelper::delete($ev->image);
                        $ev->delete();
                    }

                    foreach ($village->activities as $act) {
                        CloudinaryHelper::delete($act->image);
                        $act->delete();
                    }

                    foreach ($village->personalities as $pers) {
                        CloudinaryHelper::delete($pers->image);
                        $pers->delete();
                    }

                    foreach ($village->professionals as $pro) {
                        CloudinaryHelper::delete($pro->image);
                        $pro->delete();
                    }

                    $village->delete();
                }

                $group->delete();
            }

            // Supprimer types de divisions & divisions administratives
            $country->administrativeDivisionTypes()->delete();
            $country->administrativeDivisions()->delete();
            $country->delete();

            DB::commit();
            return redirect()->route('dashboard.pays.index')->with('success', 'Pays et TOUTES ses données rattachées (Divisions, Groupements, Villages, Acteurs) ont été supprimés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression du pays : ' . $e->getMessage());
        }
    }
}
