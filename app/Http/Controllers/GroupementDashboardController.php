<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use App\Models\VillageGroup;
use App\Models\AdministrativeDivision;
use App\Models\Country;
use App\Models\Village;
use App\Models\Event;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupementDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $groupementsQuery = VillageGroup::with('parent');

            if ($request->has('search') && !empty($request->search)) {
                $groupementsQuery->where('name', 'like', '%' . $request->search . '%')
                                 ->orWhere('chef_groupement', 'like', '%' . $request->search . '%');
            }

            $groupementsPaginator = $groupementsQuery->paginate(9)->withQueryString();

            $groupements = $groupementsPaginator->map(function ($group) {
                $chefImg = $group->chef_image ? CloudinaryHelper::url($group->chef_image) : null;
                $mainImg = $group->image ? CloudinaryHelper::url($group->image) : null;

                $hierarchy = $this->resolveDivisionHierarchy($group->parent_id);

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'chef_groupement' => $group->chef_groupement,
                    'chef_image' => $chefImg,
                    'image' => $mainImg,
                    'parent' => $group->parent ? ['name' => $group->parent->name] : null,
                    'region_id' => $hierarchy['region_id'],
                    'department_id' => $hierarchy['department_id'],
                    'arrondissement_id' => $hierarchy['arrondissement_id'],
                ];
            });

            $regions = AdministrativeDivision::where('country_id', 237)->whereNull('parent_id')->orderBy('name')->get();

            return view('dashboard.groupements.index', [
                'groupements' => $groupements,
                'paginator' => $groupementsPaginator,
                'regions' => $regions
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des groupements', ['message' => $e->getMessage()]);
            return view('dashboard.groupements.index', ['groupements' => collect([])])
                ->with('error', 'Erreur lors du chargement des groupements.');
        }
    }

    public function create()
    {
        try {
            $countries = Country::all();
            $apiBaseUrl = config('app.url') . '/api/v1';
            return view('dashboard.groupements.create', compact('countries', 'apiBaseUrl'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des pays pour création', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.groupements.index')
                ->with('error', 'Impossible de charger les pays.');
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'chef_groupement' => 'nullable|string|max:100',
            'histoire' => 'nullable|string',
            'pays' => 'nullable',
            'division3' => 'nullable',
            'chef_image' => 'nullable|image|max:10240',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée pour store groupement', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $group = new VillageGroup();
            $group->name = $request->name;
            $group->description = $request->description;
            $group->chef_groupement = \App\Helpers\FormatHelper::formatChefName($request->chef_groupement);
            $group->histoire = $request->histoire;
            $group->parent_id = $request->division3 ?: $request->division2 ?: $request->division1;

            if ($request->hasFile('chef_image')) {
                $publicId = CloudinaryHelper::upload($request->file('chef_image'), 'groupements/chefs');
                $group->chef_image = $publicId;
            }

            if ($request->hasFile('image')) {
                $publicId = CloudinaryHelper::upload($request->file('image'), 'groupements/images');
                $group->image = $publicId;
            }

            if ($request->hasFile('carousel_images')) {
                $carousel = [];
                foreach (array_slice($request->file('carousel_images'), 0, 4) as $cImg) {
                    if ($cImg) {
                        $uploaded = CloudinaryHelper::upload($cImg, 'groupements/carousel');
                        if ($uploaded) {
                            $carousel[] = $uploaded;
                        }
                    }
                }
                if (!empty($carousel)) {
                    $group->carousel_images = $carousel;
                }
            }

            $group->save();

            return redirect()->route('dashboard.groupements.index')
                ->with('success', 'Groupement créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du groupement', ['message' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du groupement.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $group = VillageGroup::with('parent')->findOrFail($id);
            $division3 = $group->parent;
            $division2 = $division3 ? AdministrativeDivision::find($division3->parent_id) : null;
            $division1 = $division2 ? AdministrativeDivision::find($division2->parent_id) : null;
            $country = $division3 ? Country::find($division3->country_id) : null;

            $groupement = [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'chef_groupement' => $group->chef_groupement,
                'histoire' => $group->histoire,
                'chef_image' => $group->chef_image ? CloudinaryHelper::url($group->chef_image) : null,
                'image' => $group->image ? CloudinaryHelper::url($group->image) : null,
                'created_at' => $group->created_at,
                'updated_at' => $group->updated_at,
                'country' => $country ? ['name' => $country->name] : null,
                'division1' => $division1 ? ['name' => $division1->name] : null,
                'division2' => $division2 ? ['name' => $division2->name] : null,
                'division3' => $division3 ? ['name' => $division3->name] : null,
            ];

            return view('dashboard.groupements.show', compact('groupement'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du groupement', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('dashboard.groupements.index')
                ->with('error', "Groupement non trouvé (ID: $id)");
        }
    }

    public function edit($id)
    {
        try {
            $group = VillageGroup::with('parent')->findOrFail($id);
            $countries = Country::all();
            $division3 = $group->parent;
            $division2 = $division3 ? AdministrativeDivision::find($division3->parent_id) : null;
            $division1 = $division2 ? AdministrativeDivision::find($division2->parent_id) : null;

            $groupement = [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'chef_groupement' => $group->chef_groupement,
                'histoire' => $group->histoire,
                'chef_image' => $group->chef_image ? CloudinaryHelper::url($group->chef_image) : null,
                'image' => $group->image ? CloudinaryHelper::url($group->image) : null,
                'country_id' => $division3 ? $division3->country_id : null,
                'division1_id' => $division1 ? $division1->id : null,
                'division2_id' => $division2 ? $division2->id : null,
                'division3_id' => $group->parent_id,
            ];

            $apiBaseUrl = config('app.url') . '/api/v1';

            return view('dashboard.groupements.edit', compact('groupement', 'countries', 'apiBaseUrl'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du groupement pour édition', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('dashboard.groupements.index')
                ->with('error', "Groupement non trouvé (ID: $id)");
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'chef_groupement' => 'nullable|string|max:100',
            'histoire' => 'nullable|string',
            'pays' => 'nullable',
            'division3' => 'nullable',
            'chef_image' => 'nullable|image|max:10240',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée pour update groupement', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $group = VillageGroup::findOrFail($id);
            $group->name = $request->name;
            $group->description = $request->description;
            $group->chef_groupement = \App\Helpers\FormatHelper::formatChefName($request->chef_groupement);
            $group->histoire = $request->histoire;
            if ($request->filled('division3') || $request->filled('division2') || $request->filled('division1')) {
                $group->parent_id = $request->division3 ?: $request->division2 ?: $request->division1;
            }

            if ($request->hasFile('chef_image')) {
                CloudinaryHelper::delete($group->chef_image);
                $group->chef_image = CloudinaryHelper::upload($request->file('chef_image'), 'groupements/chefs');
            }

            if ($request->hasFile('image')) {
                CloudinaryHelper::delete($group->image);
                $group->image = CloudinaryHelper::upload($request->file('image'), 'groupements/images');
            }

            if ($request->hasFile('carousel_images')) {
                $carousel = is_array($group->carousel_images) ? $group->carousel_images : [];
                foreach (array_slice($request->file('carousel_images'), 0, 4) as $cImg) {
                    if ($cImg) {
                        $uploaded = CloudinaryHelper::upload($cImg, 'groupements/carousel');
                        if ($uploaded) {
                            $carousel[] = $uploaded;
                        }
                    }
                }
                $group->carousel_images = array_slice($carousel, 0, 4);
            }

            $group->save();

            return redirect()->route('dashboard.groupements.index')
                ->with('success', 'Groupement mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du groupement', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du groupement.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Récupérer le groupement avec ses villages et personnalités administratives
            $group = VillageGroup::with([
                'villages' => function ($query) {
                    $query->with([
                        'events.contributions',
                        'activities',
                        'personalities',
                        'professionals'
                    ]);
                },
                'personnalitesAdministratives'
            ])->findOrFail($id);

            Log::info('Suppression groupement initiée', [
                'groupement_id' => $id,
                'groupement_name' => $group->name,
                'villages_count' => $group->villages->count(),
                'personnalites_administratives_count' => $group->personnalitesAdministratives->count(),
            ]);

            // Supprimer les images du groupement
            CloudinaryHelper::delete($group->chef_image);
            Log::info('Suppression chef_image réussie', ['public_id' => $group->chef_image]);

            CloudinaryHelper::delete($group->image);
            Log::info('Suppression image réussie', ['public_id' => $group->image]);

            // Supprimer les personnalités administratives
            foreach ($group->personnalitesAdministratives as $personnalite) {
                CloudinaryHelper::delete($personnalite->photo);
                Log::info('Photo personnalité administrative supprimée de Cloudinary', ['public_id' => $personnalite->photo]);

                $personnalite->delete();
                Log::info('Personnalité administrative supprimée', [
                    'personnalite_id' => $personnalite->id,
                    'nom' => $personnalite->nom,
                    'prenom' => $personnalite->prenom,
                ]);
            }

            // Supprimer les villages et leurs données associées
            foreach ($group->villages as $village) {
                // Supprimer les images du village
                CloudinaryHelper::delete($village->village_image);
                Log::info('Image village supprimée de Cloudinary', ['public_id' => $village->village_image]);

                CloudinaryHelper::delete($village->chief_image);
                Log::info('Image chef village supprimée de Cloudinary', ['public_id' => $village->chief_image]);

                // Supprimer les événements et leurs contributions
                foreach ($village->events as $event) {
                    $contributionsCount = $event->contributions->count();
                    $event->contributions()->delete();
                    Log::info('Contributions supprimées pour événement', [
                        'event_id' => $event->id,
                        'event_name' => $event->name,
                        'contributions_count' => $contributionsCount,
                    ]);

                    CloudinaryHelper::delete($event->image);
                    Log::info('Image événement supprimée de Cloudinary', ['public_id' => $event->image]);

                    $event->delete();
                    Log::info('Événement supprimé', [
                        'event_id' => $event->id,
                        'event_name' => $event->name,
                    ]);
                }

                // Supprimer les activités
                foreach ($village->activities as $activity) {
                    CloudinaryHelper::delete($activity->image);
                    Log::info('Image activité supprimée de Cloudinary', ['public_id' => $activity->image]);

                    $activity->delete();
                    Log::info('Activité supprimée', [
                        'activity_id' => $activity->id,
                        'activity_name' => $activity->name,
                    ]);
                }

                // Supprimer les personnalités
                foreach ($village->personalities as $personality) {
                    CloudinaryHelper::delete($personality->image);
                    Log::info('Image personnalité supprimée de Cloudinary', ['public_id' => $personality->image]);

                    $personality->delete();
                    Log::info('Personnalité supprimée', [
                        'personality_id' => $personality->id,
                        'personality_name' => $personality->name,
                    ]);
                }

                // Supprimer les professionnels
                foreach ($village->professionals as $professional) {
                    CloudinaryHelper::delete($professional->image);
                    Log::info('Image professionnel supprimée de Cloudinary', ['public_id' => $professional->image]);

                    $professional->delete();
                    Log::info('Professionnel supprimé', [
                        'professional_id' => $professional->id,
                        'professional_name' => $professional->name,
                    ]);
                }

                // Supprimer le village
                $village->delete();
                Log::info('Village supprimé', [
                    'village_id' => $village->id,
                    'village_name' => $village->name,
                ]);
            }

            // Supprimer le groupement
            $group->delete();
            Log::info('Groupement supprimé avec succès', [
                'groupement_id' => $id,
                'groupement_name' => $group->name,
            ]);

            DB::commit();
            return redirect()->route('dashboard.groupements.index')
                ->with('success', 'Groupement et toutes ses données associées supprimées avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Groupement non trouvé pour suppression', ['id' => $id]);
            return redirect()->route('dashboard.groupements.index')
                ->with('error', 'Groupement non trouvé.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du groupement', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('dashboard.groupements.index')
                ->with('error', 'Erreur lors de la suppression du groupement.');
        }
    }

    public function list()
    {
        try {
            $groupements = VillageGroup::with('parent')->get()->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'chef_image' => $group->chef_image ? CloudinaryHelper::url($group->chef_image) : null,
                    'image' => $group->image ? CloudinaryHelper::url($group->image) : null,
                ];
            });

            return view('liste.grou', compact('groupements'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la liste publique', ['message' => $e->getMessage()]);
            return view('liste.grou', ['groupements' => collect([])])
                ->with('error', 'Erreur lors du chargement des groupements.');
        }
    }

    public function publicShow($id)
    {
        try {
            Log::info('Début de publicShow', ['id' => $id]);

            // Étape 1 : Vérifier le groupement par ID ou Slug
            $realId = intval(explode('-', (string)$id)[0]);
            $group = null;
            if ($realId > 0) {
                $group = VillageGroup::with('parent')->find($realId);
            }
            if (!$group) {
                $searchName = str_replace('-', ' ', (string)$id);
                $group = VillageGroup::with('parent')->where('name', 'like', '%' . $searchName . '%')->firstOrFail();
            }
            Log::info('Groupement trouvé', ['id' => $group->id, 'name' => $group->name]);

            // Étape 2 : Récupérer les villages
            $villages = Village::where('village_group_id', $group->id)->get();
            Log::info('Villages récupérés', [
                'count' => $villages->count(),
                'villages' => $villages->pluck('id')->toArray()
            ]);

            // Étape 3 : Vérifier les divisions administratives
            $villageData = $villages->map(function ($village) {
                Log::info('Vérification du village', [
                    'village_id' => $village->id,
                    'name' => $village->name,
                    'administrative_division_id' => $village->administrative_division_id
                ]);

                // Vérifier manuellement la division administrative
                $division = $village->administrative_division_id
                    ? AdministrativeDivision::find($village->administrative_division_id)
                    : null;
                Log::info('Division administrative', [
                    'village_id' => $village->id,
                    'division' => $division ? $division->toArray() : null
                ]);

                $events = Event::where('village_id', $village->id)->get()->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'name' => $event->name,
                        'type' => $event->type,
                        'description' => $event->description,
                        'start_date' => $event->start_date,
                        'end_date' => $event->end_date,
                        'image' => $event->image ? Storage::url($event->image) : null,
                        'village_id' => $event->village_id,
                    ];
                });

                return [
                    'id' => $village->id,
                    'name' => $village->name,
                    'description' => $village->description,
                    'image' => $village->village_image ? Storage::url($village->village_image) : null, // Correction ici
                    'population' => $village->population,
                    'events' => $events->toArray(),
                ];
            });

            // Étape 4 : Récupérer les divisions et le pays
            Log::info('Récupération des divisions');
            $division3 = $group->parent;
            Log::info('Division3', ['division3' => $division3 ? $division3->toArray() : null]);

            $division2 = $division3 ? AdministrativeDivision::find($division3->parent_id) : null;
            Log::info('Division2', ['division2' => $division2 ? $division2->toArray() : null]);

            $division1 = $division2 ? AdministrativeDivision::find($division2->parent_id) : null;
            Log::info('Division1', ['division1' => $division1 ? $division1->toArray() : null]);

            $country = $division3 ? Country::find($division3->country_id) : null;
            Log::info('Country', ['country' => $country ? $country->toArray() : null]);

            $pas = \App\Models\PersonnaliteAdministrative::where('village_group_id', $group->id)->get();

            // Étape 5 : Construire les données du groupement
            $groupement = [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'chef_groupement' => $group->chef_groupement,
                'histoire' => $group->histoire,
                'chef_image' => $group->chef_image ? (Str::startsWith($group->chef_image, ['http://', 'https://']) ? $group->chef_image : Storage::url($group->chef_image)) : null,
                'image' => $group->image ? (Str::startsWith($group->image, ['http://', 'https://']) ? $group->image : Storage::url($group->image)) : null,
                'village_count' => $villages->count(),
                'population' => $villages->sum('population'),
                'country' => $country ? ['name' => $country->name] : null,
                'division1' => $division1 ? ['name' => $division1->name] : null,
                'division2' => $division2 ? ['name' => $division2->name] : null,
                'division3' => $division3 ? ['name' => $division3->name] : null,
                'villages' => $villageData->values()->toArray(),
                'personnalites_administratives' => $pas,
            ];
            Log::info('Groupement construit', ['groupement_id' => $groupement['id']]);

            // Étape 6 : Rendu de la vue
            $group = $groupement;
            view()->share('currentGroupement', $groupement);
            Log::info('Rendu de la vue', ['vue' => 'groupementPage']);
            return view('groupement.groupementPage', compact('groupement', 'group'));
        } catch (\Exception $e) {
            Log::error('Erreur dans publicShow', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('liste.groupements')
                ->with('error', 'Erreur lors du chargement du groupement.');
        }
    }

    private function resolveDivisionHierarchy($divisionId)
    {
        if (!$divisionId) return ['region_id' => null, 'department_id' => null, 'arrondissement_id' => null];
        $div = AdministrativeDivision::find($divisionId);
        if (!$div) return ['region_id' => null, 'department_id' => null, 'arrondissement_id' => null];

        if ($div->parent_id) {
            $parentDiv = AdministrativeDivision::find($div->parent_id);
            if ($parentDiv && $parentDiv->parent_id) {
                return [
                    'arrondissement_id' => $div->id,
                    'department_id' => $parentDiv->id,
                    'region_id' => $parentDiv->parent_id,
                ];
            } else if ($parentDiv) {
                return [
                    'arrondissement_id' => null,
                    'department_id' => $div->id,
                    'region_id' => $parentDiv->id,
                ];
            }
        }
        return [
            'arrondissement_id' => null,
            'department_id' => null,
            'region_id' => $div->id,
        ];
    }
}
