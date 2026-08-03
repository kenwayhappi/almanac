<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use App\Models\Country;
use App\Models\Village;
use App\Models\VillageGroup;
use App\Models\Advertisement;
use App\Models\Personality;
use App\Models\Professional;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VillageDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Village::with('villageGroup');

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $villages = $query->paginate(9)->withQueryString();
        $allGroupements = VillageGroup::select('id', 'name')->get();
        return view('dashboard.villages.index', compact('villages', 'allGroupements'));
    }

    public function create()
    {
        $groupements = VillageGroup::all();
        $countries = Country::all();
        return view('dashboard.villages.create', compact('groupements', 'countries'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'histoire' => 'nullable|string',
            'population' => 'nullable|integer',
            'village_image' => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
            'chief_image' => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'name', 'description', 'histoire', 'population', 'chef_village',
            'is_village', 'village_group_id', 'current_chief', 'chief_description',
            'chief_achievements', 'chief_interventions', 'village_history', 'historical_dynasty'
        ]);

        $data['is_village'] = $request->input('is_village', 1) == 1 ? 1 : 0;
        if ($data['is_village'] == 0) {
            $data['chef_village'] = null;
            $data['current_chief'] = null;
            $data['chief_description'] = null;
            $data['chief_achievements'] = null;
            $data['chief_image'] = null;
        } else {
            if ($request->hasFile('chief_image')) {
                $data['chief_image'] = CloudinaryHelper::upload($request->file('chief_image'), 'villages/chiefs');
            }
        }

        if ($request->hasFile('village_image')) {
            $data['village_image'] = CloudinaryHelper::upload($request->file('village_image'), 'villages/images');
        }

        $village = Village::create($data);
        return redirect()->route('dashboard.villages.index')->with('success', $data['is_village'] ? 'Village créé avec succès.' : 'Quartier créé avec succès.');
    }

    public function show($id)
    {
        $village = Village::with('villageGroup')->findOrFail($id);
        return view('dashboard.villages.show', compact('village'));
    }

    public function edit($id)
    {
        $village = Village::with('villageGroup')->findOrFail($id);
        $groupements = VillageGroup::all();
        $countries = Country::all();
        return view('dashboard.villages.edit', compact('village', 'groupements', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'histoire' => 'nullable|string',
            'population' => 'nullable|integer',
            'village_image' => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
            'chief_image' => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $village = Village::findOrFail($id);
        $data = $request->only([
            'name', 'description', 'histoire', 'population', 'chef_village',
            'is_village', 'village_group_id', 'current_chief', 'chief_description',
            'chief_achievements', 'chief_interventions', 'village_history', 'historical_dynasty'
        ]);

        $data['is_village'] = $request->input('is_village', 1) == 1 ? 1 : 0;
        if ($data['is_village'] == 0) {
            CloudinaryHelper::delete($village->chief_image);
            $data['chef_village'] = null;
            $data['current_chief'] = null;
            $data['chief_description'] = null;
            $data['chief_achievements'] = null;
            $data['chief_image'] = null;
        } else {
            if ($request->hasFile('chief_image')) {
                CloudinaryHelper::delete($village->chief_image);
                $data['chief_image'] = CloudinaryHelper::upload($request->file('chief_image'), 'villages/chiefs');
            }
        }

        if ($request->hasFile('village_image')) {
            CloudinaryHelper::delete($village->village_image);
            $data['village_image'] = CloudinaryHelper::upload($request->file('village_image'), 'villages/images');
        }

        $village->update($data);
        return redirect()->route('dashboard.villages.index')->with('success', $data['is_village'] ? 'Village mis à jour avec succès.' : 'Quartier mis à jour avec succès.');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Récupérer le village avec toutes ses relations
            $village = Village::with([
                'villageGroup',
                'events.contributions',
                'activities',
                'personalities',
                'professionals'
            ])->findOrFail($id);

            Log::info('Suppression village initiée', [
                'village_id' => $id,
                'village_name' => $village->name,
                'events_count' => $village->events->count(),
                'activities_count' => $village->activities->count(),
                'personalities_count' => $village->personalities->count(),
                'professionals_count' => $village->professionals->count(),
            ]);

            // Supprimer les images du village
            CloudinaryHelper::delete($village->village_image);
            Log::info('Image village supprimée de Cloudinary', ['public_id' => $village->village_image]);

            CloudinaryHelper::delete($village->chief_image);
            Log::info('Image chef supprimée de Cloudinary', ['public_id' => $village->chief_image]);

            // Supprimer les événements et leurs contributions
            foreach ($village->events as $event) {
                // Supprimer les contributions de l'événement
                $contributionsCount = $event->contributions->count();
                $event->contributions()->delete();
                Log::info('Contributions supprimées pour événement', [
                    'event_id' => $event->id,
                    'event_name' => $event->name,
                    'contributions_count' => $contributionsCount,
                ]);

                // Supprimer l'image de l'événement
                CloudinaryHelper::delete($event->image);
                Log::info('Image événement supprimée de Cloudinary', ['public_id' => $event->image]);

                // Supprimer l'événement
                $event->delete();
                Log::info('Événement supprimé', [
                    'event_id' => $event->id,
                    'event_name' => $event->name,
                ]);
            }

            // Supprimer les activités
            foreach ($village->activities as $activity) {
                // Supprimer l'image de l'activité
                CloudinaryHelper::delete($activity->image);
                Log::info('Image activité supprimée de Cloudinary', ['public_id' => $activity->image]);

                // Supprimer l'activité
                $activity->delete();
                Log::info('Activité supprimée', [
                    'activity_id' => $activity->id,
                    'activity_name' => $activity->name,
                ]);
            }

            // Supprimer les personnalités
            foreach ($village->personalities as $personality) {
                // Supprimer l'image de la personnalité
                CloudinaryHelper::delete($personality->image);
                Log::info('Image personnalité supprimée de Cloudinary', ['public_id' => $personality->image]);

                // Supprimer la personnalité
                $personality->delete();
                Log::info('Personnalité supprimée', [
                    'personality_id' => $personality->id,
                    'personality_name' => $personality->name,
                ]);
            }

            // Supprimer les professionnels
            foreach ($village->professionals as $professional) {
                // Supprimer l'image du professionnel
                CloudinaryHelper::delete($professional->image);
                Log::info('Image professionnel supprimée de Cloudinary', ['public_id' => $professional->image]);

                // Supprimer le professionnel
                $professional->delete();
                Log::info('Professionnel supprimé', [
                    'professional_id' => $professional->id,
                    'professional_name' => $professional->name,
                ]);
            }

            // Supprimer le village
            $village->delete();
            Log::info('Village supprimé avec succès', [
                'village_id' => $id,
                'village_name' => $village->name,
            ]);

            DB::commit();
            return redirect()->route('dashboard.villages.index')->with('success', 'Village et toutes ses données associées supprimées avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Village non trouvé pour suppression', ['id' => $id]);
            return redirect()->route('dashboard.villages.index')->with('error', 'Village non trouvé.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du village', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('dashboard.villages.index')->with('error', 'Erreur lors de la suppression du village.');
        }
    }

    public function publicShow($id)
    {
        $realId = intval(explode('-', (string)$id)[0]);
        $village = null;
        if ($realId > 0) {
            $village = Village::with(['villageGroup', 'activities', 'personalities', 'professionals', 'events'])->find($realId);
        }
        if (!$village) {
            $searchName = str_replace('-', ' ', (string)$id);
            $village = Village::with(['villageGroup', 'activities', 'personalities', 'professionals', 'events'])
                ->where('name', 'like', '%' . $searchName . '%')
                ->firstOrFail();
        }
        return view('village.index', compact('village'));
    }

    public function list()
    {
        $villages = Village::all();
        return view('liste.list', compact('villages'));
    }

    public function search(Request $request)
    {
        Log::info('Search Request Parameters', $request->all());

        $countries = Country::all();
        $allGroupements = VillageGroup::select('id', 'name')->get();
        $villages = collect([]);
        $groupements = collect([]);
        $searchType = $request->input('searchType', 'villages');
        $keyword = $request->input('name') ?: $request->input('search');

        $div1 = $request->input('division1');
        $div2 = $request->input('division2');
        $arrond = $request->input('arrondissement');
        $groupementId = $request->input('division3');

        // Requête pour les villages
        $villageQuery = Village::query()->with('villageGroup');

        if (!empty($keyword)) {
            $villageQuery->where('name', 'like', '%' . $keyword . '%');
        }

        if ($groupementId) {
            $villageQuery->where('village_group_id', $groupementId);
        } else if ($arrond) {
            $villageQuery->whereHas('villageGroup', function ($q) use ($arrond) {
                $q->where('parent_id', $arrond);
            });
        } else if ($div2) {
            $villageQuery->whereHas('villageGroup.parent', function ($q) use ($div2) {
                $q->where('id', $div2)->orWhere('parent_id', $div2);
            });
        } else if ($div1) {
            $villageQuery->whereHas('villageGroup.parent', function ($q) use ($div1) {
                $q->where('id', $div1)
                  ->orWhere('parent_id', $div1)
                  ->orWhereHas('parent', function($q2) use ($div1) {
                      $q2->where('parent_id', $div1);
                  });
            });
        }

        $villages = $villageQuery->paginate(4, ['*'], 'villages_page')->withQueryString();

        // Requête pour les groupements
        $groupementQuery = VillageGroup::query()->withCount('villages');

        if (!empty($keyword)) {
            $groupementQuery->where(function($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('chef_groupement', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        if ($groupementId) {
            $groupementQuery->where('id', $groupementId);
        } else if ($arrond) {
            $groupementQuery->where('parent_id', $arrond);
        } else if ($div2) {
            $groupementQuery->whereHas('parent', function ($q) use ($div2) {
                $q->where('id', $div2)->orWhere('parent_id', $div2);
            });
        } else if ($div1) {
            $groupementQuery->whereHas('parent', function ($q) use ($div1) {
                $q->where('id', $div1)
                  ->orWhere('parent_id', $div1)
                  ->orWhereHas('parent', function($q2) use ($div1) {
                      $q2->where('parent_id', $div1);
                  });
            });
        }

        $groupements = $groupementQuery->paginate(4, ['*'], 'groupements_page')->withQueryString();

        $advertisements = Advertisement::where('position', 'rechercher')
            ->whereIn('type', ['video', 'photo', 'pdf', 'text'])
            ->get()
            ->map(function ($ad) {
                if (!empty($ad->file_path)) {
                    $ad->file_url = CloudinaryHelper::url($ad->file_path);
                }
                return $ad;
            });

        $initialAds = $advertisements->shuffle()->take(3)->values();

        return view('recherche', compact(
            'villages',
            'groupements',
            'countries',
            'allGroupements',
            'advertisements',
            'initialAds',
            'searchType',
            'keyword'
        ));
    }

    public function decouvrir($id)
    {
        $village = Village::with('villageGroup')->findOrFail($id);
        return view('village.decouvrir', compact('village'));
    }

    public function personnalite($id)
    {
        try {
            $village = Village::findOrFail($id);
            $personalities = Personality::where('village_id', $id)->get();
            Log::info('Village et personnalités chargés pour affichage', [
                'village_id' => $id,
                'village_name' => $village->name,
                'personalities_count' => $personalities->count(),
            ]);
            return view('village.personnalite', compact('village', 'personalities'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Village non trouvé pour personnalités : ', ['id' => $id]);
            return redirect()->route('home')->with('error', 'Village non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur chargement personnalités village : ', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('home')->with('error', 'Erreur lors du chargement des personnalités.');
        }
    }

    public function artisant($id)
    {
        try {
            $village = Village::with('villageGroup')->findOrFail($id);
            // Récupérer le groupement du village
            $villageGroupId = $village->village_group_id;

            // Récupérer tous les villages du même groupement
            $villagesInGroup = Village::where('village_group_id', $villageGroupId)->pluck('id');

            // Récupérer tous les professionnels des villages du groupement
            $professionals = Professional::whereIn('village_id', $villagesInGroup)
                ->with('village') // Charger la relation village pour afficher le village d'origine
                ->get()
                ->map(function ($professional) {
                    $professional->image_url = $professional->image ? Storage::url($professional->image) : null;
                    return $professional;
                });

            Log::info('Village et artisans chargés pour affichage', [
                'village_id' => $id,
                'village_name' => $village->name,
                'professionals_count' => $professionals->count(),
                'groupement_id' => $villageGroupId,
            ]);

            return view('village.artisant', compact('village', 'professionals'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Village non trouvé pour artisans : ', ['id' => $id]);
            return redirect()->route('home')->with('error', 'Village non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur chargement artisans village : ', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('home')->with('error', 'Erreur lors du chargement des artisans.');
        }
    }

    public function ensavoirplus($id)
    {
        $village = Village::with('villageGroup')->findOrFail($id);
        return view('village.ensavoirplus', compact('village'));
    }
}
