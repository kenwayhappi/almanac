<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Country;
use App\Models\Village;
use App\Models\VillageGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ActivityDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('village');
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('type', 'like', '%' . $request->search . '%');
        }
        $activites = $query->paginate(9)->withQueryString();
        $countries = Country::all();
        $villageGroups = VillageGroup::all();
        $villages = Village::all();

        return view('dashboard.activites.index', compact('activites', 'countries', 'villageGroups', 'villages'));
    }

    public function create()
    {
        $countries = Country::all();
        $villages = Village::with('villageGroup.parent')->get()->map(function ($village) {
            return [
                'id' => $village->id,
                'name' => $village->name,
                'country_id' => $village->villageGroup && $village->villageGroup->parent ? $village->villageGroup->parent->country_id : null,
            ];
        })->toArray();

        Log::info('Pays récupérés pour création activité : ', $countries->toArray());
        Log::info('Villages enrichis pour création activité : ', $villages);

        return view('dashboard.activites.create', compact('countries', 'villages'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:Festival,Atelier,Cérémonie,Autre',
            'description' => 'nullable|string',
            'country_id' => 'required|exists:countries,id',
            'village_id' => 'required|exists:villages,id',
            'image' => 'nullable|image|max:10240|mimes:jpg,png,jpeg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'type', 'description', 'village_id']);

        if ($request->hasFile('image')) {
            $publicId = CloudinaryHelper::upload($request->file('image'), 'activites/images');
            $data['image'] = $publicId;
        }

        $activity = Activity::create($data);

        return redirect()->route('dashboard.activites.index')->with('success', 'Activité créée avec succès.');
    }

    public function show($id)
    {
        $activity = Activity::with('village')->findOrFail($id);
        return view('dashboard.activites.show', compact('activity'));
    }

    public function edit($id)
    {
        $activity = Activity::with('village')->findOrFail($id);
        $villages = Village::all();

        return view('dashboard.activites.edit', compact('activity', 'villages'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:Festival,Atelier,Cérémonie,Autre',
            'description' => 'nullable|string',
            'village_id' => 'required|exists:villages,id',
            'image' => 'nullable|image|max:10240|mimes:jpg,png,jpeg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $activity = Activity::findOrFail($id);
        $data = $request->only(['name', 'type', 'description', 'village_id']);

        if ($request->hasFile('image')) {
            CloudinaryHelper::delete($activity->image);
            $publicId = CloudinaryHelper::upload($request->file('image'), 'activites/images');
            $data['image'] = $publicId;
        }

        $activity->update($data);

        return redirect()->route('dashboard.activites.index')->with('success', 'Activité mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);

        CloudinaryHelper::delete($activity->image);

        $activity->delete();

        return redirect()->route('dashboard.activites.index')->with('success', 'Activité supprimée avec succès.');
    }
}