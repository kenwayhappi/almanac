<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonnaliteAdministrative;
use App\Models\VillageGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PersonnaliteAdministrativeController extends Controller
{
    /**
     * Afficher toutes les personnalités administratives.
     */
    public function index()
    {
        try {
            $personnalites = PersonnaliteAdministrative::with('villageGroup')->get();
            Log::info('Personnalités récupérées', ['count' => $personnalites->count()]);
            return response()->json([
                'success' => true,
                'data' => $personnalites
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur index personnalités : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
        }
    }

    /**
     * Afficher les données pour le formulaire de création.
     */
    public function create()
    {
        try {
            $villageGroups = VillageGroup::all();
            Log::info('Groupes de villages pour création', ['count' => $villageGroups->count()]);
            return response()->json([
                'success' => true,
                'data' => $villageGroups
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur create personnalités : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
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
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation store personnalité : ', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('personnalites/photos', 'public');
                $data['photo'] = $path;
                Log::info('Photo stockée', ['path' => $path]);
            }

            $personnalite = PersonnaliteAdministrative::create($data);
            $personnalite->load('villageGroup');

            return response()->json([
                'success' => true,
                'data' => $personnalite
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur store personnalité : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création'
            ], 500);
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
            return response()->json([
                'success' => true,
                'data' => $personnalite
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée : ', ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Personnalité non trouvée'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur show personnalité : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
        }
    }

    /**
     * Afficher les données pour modifier une personnalité.
     */
    public function edit($id)
    {
        try {
            $personnalite = PersonnaliteAdministrative::with('villageGroup')->findOrFail($id);
            $villageGroups = VillageGroup::all();
            Log::info('Données pour modification', ['personnalite_id' => $id, 'village_groups_count' => $villageGroups->count()]);
            return response()->json([
                'success' => true,
                'data' => [
                    'personnalite' => $personnalite,
                    'villageGroups' => $villageGroups
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée pour edit : ', ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Personnalité non trouvée'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur edit personnalité : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
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
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation update personnalité : ', ['id' => $id, 'errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            if ($request->hasFile('photo')) {
                CloudinaryHelper::delete($personnalite->getRawOriginal('photo'));
                Log::info('Ancienne photo supprimée de Cloudinary', ['public_id' => $personnalite->getRawOriginal('photo')]);
                $publicId = CloudinaryHelper::upload($request->file('photo'), 'personnalites/photos');
                $data['photo'] = $publicId;
                Log::info('Nouvelle photo uploadée sur Cloudinary', ['public_id' => $publicId]);
            }

            $personnalite->update($data);
            $personnalite->load('villageGroup');

            return response()->json([
                'success' => true,
                'data' => $personnalite
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée pour update : ', ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Personnalité non trouvée'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur update personnalité : ', ['id' => $id, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    /**
     * Supprimer une personnalité administrative.
     */
    public function destroy($id)
    {
        try {
            $personnalite = PersonnaliteAdministrative::findOrFail($id);

            CloudinaryHelper::delete($personnalite->getRawOriginal('photo'));
            Log::info('Photo supprimée de Cloudinary lors de la suppression', ['public_id' => $personnalite->getRawOriginal('photo')]);

            $personnalite->delete();
            Log::info('Personnalité supprimée', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Personnalité supprimée'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée pour destroy : ', ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Personnalité non trouvée'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur destroy personnalité : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }

    /**
     * Afficher les personnalités d'un groupe de villages spécifique.
     */
    public function showVillageGroupPersonnalites($villageGroupId)
    {
        try {
            $personnalites = PersonnaliteAdministrative::where('village_group_id', $villageGroupId)
                ->with('villageGroup')
                ->get();
            Log::info('Personnalités pour groupe', ['village_group_id' => $villageGroupId, 'count' => $personnalites->count()]);
            return response()->json([
                'success' => true,
                'data' => $personnalites
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur showVillageGroupPersonnalites : ', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
        }
    }
}

