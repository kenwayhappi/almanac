<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use App\Models\PersonnaliteAdministrative;
use App\Models\VillageGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PersonnaliteAdministrativeController extends Controller
{
    /**
     * Afficher la liste des personnalités administratives.
     */
    public function index(Request $request)
    {
        try {
            $query = PersonnaliteAdministrative::with('villageGroup');
            if ($request->has('search') && !empty($request->search)) {
                $query->where('nom', 'like', '%' . $request->search . '%')
                      ->orWhere('prenom', 'like', '%' . $request->search . '%')
                      ->orWhere('role', 'like', '%' . $request->search . '%');
            }
            $personnalites = $query->paginate(9)->withQueryString();
            $villageGroups = VillageGroup::all();
            Log::info('Personnalités récupérées', ['count' => $personnalites->count()]);
            return view('dashboard.personnalites_administratives.index', compact('personnalites', 'villageGroups'));
        } catch (\Exception $e) {
            Log::error('Erreur index personnalités : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('error', 'Erreur lors du chargement des personnalités.');
        }
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        try {
            $villageGroups = VillageGroup::all();
            Log::info('Groupes de villages pour création', ['count' => $villageGroups->count()]);
            return view('dashboard.personnalites_administratives.create', compact('villageGroups'));
        } catch (\Exception $e) {
            Log::error('Erreur create personnalités : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Enregistrer une nouvelle personnalité administrative.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'village_group_id' => 'required|exists:village_groups,id',
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'biographie' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation store personnalité : ', ['errors' => $validator->errors()->toArray()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->all();
            if ($request->hasFile('photo')) {
                $publicId = CloudinaryHelper::upload($request->file('photo'), 'personnalites/photos');
                $data['photo'] = $publicId;
                Log::info('Photo uploadée sur Cloudinary', ['public_id' => $publicId]);
            }

            PersonnaliteAdministrative::create($data);
            return redirect()->route('dashboard.personnalites_administratives.index')
                            ->with('success', 'Personnalité administrative créée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur store personnalité : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Erreur lors de la création.')->withInput();
        }
    }

    /**
     * Afficher une personnalité administrative spécifique.
     */
    public function show($id)
    {
        try {
            $personnalite = PersonnaliteAdministrative::with('villageGroup')->findOrFail($id);
            Log::info('Personnalité récupérée', ['id' => $id]);
            return view('dashboard.personnalites_administratives.show', compact('personnalite'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée : ', ['id' => $id]);
            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('error', 'Personnalité non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur show personnalité : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('error', 'Erreur lors du chargement de la personnalité.');
        }
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit($id)
    {
        try {
            $personnalite = PersonnaliteAdministrative::with('villageGroup')->findOrFail($id);
            $villageGroups = VillageGroup::all();
            Log::info('Données pour modification', ['personnalite_id' => $id, 'village_groups_count' => $villageGroups->count()]);
            return view('dashboard.personnalites_administratives.edit', compact('personnalite', 'villageGroups'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée pour edit : ', ['id' => $id]);
            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('error', 'Personnalité non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur edit personnalité : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Mettre à jour une personnalité administrative.
     */
    public function update(Request $request, $id)
    {
        try {
            $personnalite = PersonnaliteAdministrative::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'village_group_id' => 'required|exists:village_groups,id',
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'biographie' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation update personnalité : ', ['id' => $id, 'errors' => $validator->errors()->toArray()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->all();
            if ($request->hasFile('photo')) {
                CloudinaryHelper::delete($personnalite->photo);
                Log::info('Ancienne photo supprimée de Cloudinary', ['public_id' => $personnalite->photo]);
                $publicId = CloudinaryHelper::upload($request->file('photo'), 'personnalites/photos');
                $data['photo'] = $publicId;
                Log::info('Nouvelle photo uploadée sur Cloudinary', ['public_id' => $publicId]);
            }

            $personnalite->update($data);
            return redirect()->route('dashboard.personnalites_administratives.index')
                            ->with('success', 'Personnalité administrative mise à jour avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée pour update : ', ['id' => $id]);
            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('error', 'Personnalité non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur update personnalité : ', ['id' => $id, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.')->withInput();
        }
    }

    /**
     * Supprimer une personnalité administrative.
     */
    public function destroy($id)
    {
        try {
            $personnalite = PersonnaliteAdministrative::findOrFail($id);

            CloudinaryHelper::delete($personnalite->photo);
            Log::info('Photo supprimée de Cloudinary lors de la suppression', ['public_id' => $personnalite->photo]);

            $personnalite->delete();
            Log::info('Personnalité supprimée', ['id' => $id]);

            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('success', 'Personnalité administrative supprimée avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée pour destroy : ', ['id' => $id]);
            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('error', 'Personnalité non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur destroy personnalité : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('dashboard.personnalites_administratives.index')
                           ->with('error', 'Erreur lors de la suppression.');
        }
    }
    public function showVillageGroupPersonnalites($villageGroupId)
{
    try {
        $villageGroup = VillageGroup::findOrFail($villageGroupId);
        $personnalites = PersonnaliteAdministrative::where('village_group_id', $villageGroupId)
            ->with('villageGroup')
            ->get();
        
        Log::info('Personnalités pour groupe', [
            'village_group_id' => $villageGroupId,
            'count' => $personnalites->count()
        ]);

        return view('groupement.personnalites_administratives', [
            'currentGroupement' => $villageGroup,
            'personnalites' => $personnalites
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::warning('Groupement non trouvé : ', ['id' => $villageGroupId]);
        return redirect()->route('groupement.show', ['id' => $villageGroupId])
                       ->with('error', 'Groupement non trouvé.');
    } catch (\Exception $e) {
        Log::error('Erreur showVillageGroupPersonnalites : ', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->route('groupement.show', ['id' => $villageGroupId])
                       ->with('error', 'Erreur lors du chargement des personnalités.');
    }
}
}