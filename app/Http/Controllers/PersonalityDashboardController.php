<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use App\Models\Personality;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PersonalityDashboardController extends Controller
{
    /**
     * Afficher la liste des personnalités.
     */
    public function index(Request $request)
    {
        try {
            $query = Personality::with('village');
            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('statut', 'like', '%' . $request->search . '%');
            }
            $personalities = $query->paginate(9)->withQueryString();
            $villages = Village::all();
            Log::info('Personnalités récupérées', ['count' => $personalities->count()]);
            return view('dashboard.personnalite.index', compact('personalities', 'villages'));
        } catch (\Exception $e) {
            Log::error('Erreur index personnalités : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.personnalite.index')
                           ->with('error', 'Erreur lors du chargement des personnalités.');
        }
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        try {
            $villages = Village::all();
            Log::info('Villages pour création', ['count' => $villages->count()]);
            return view('dashboard.personnalite.create', compact('villages'));
        } catch (\Exception $e) {
            Log::error('Erreur create personnalités : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.personnalite.index')
                           ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Enregistrer une nouvelle personnalité.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'statut' => 'required|string|in:Notable,Elite,Association,Entreprise',
                'contact' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'village_id' => 'required|exists:villages,id',
                'has_paid' => 'required|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation store personnalité : ', ['errors' => $validator->errors()->toArray()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->only(['name', 'statut', 'contact', 'description', 'village_id', 'has_paid']);
            if ($request->hasFile('image')) {
                $publicId = CloudinaryHelper::upload($request->file('image'), 'personalities');
                $data['image'] = $publicId;
                Log::info('Image uploadée sur Cloudinary', ['public_id' => $publicId]);
            }

            Personality::create($data);
            return redirect()->route('dashboard.personnalite.index')
                            ->with('success', 'Personnalité créée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur store personnalité : ', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création.')->withInput();
        }
    }

    /**
     * Afficher une personnalité spécifique.
     */
    public function show($id)
    {
        try {
            $personality = Personality::with('village')->findOrFail($id);
            Log::info('Personnalité récupérée', ['id' => $id]);
            return view('dashboard.personnalite.show', compact('personality'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée : ', ['id' => $id]);
            return redirect()->route('dashboard.personnalite.index')
                           ->with('error', 'Personnalité non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur show personnalité : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.personnalite.index')
                           ->with('error', 'Erreur lors du chargement de la personnalité.');
        }
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit($id)
    {
        try {
            $personality = Personality::with('village')->findOrFail($id);
            $villages = Village::all();
            Log::info('Données pour modification', ['personnalite_id' => $id, 'villages_count' => $villages->count()]);
            return view('dashboard.personnalite.edit', compact('personality', 'villages'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée pour edit : ', ['id' => $id]);
            return redirect()->route('dashboard.personnalite.index')
                           ->with('error', 'Personnalité non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur edit personnalité : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.personnalite.index')
                           ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Mettre à jour une personnalité.
     */
    public function update(Request $request, $id)
    {
        try {
            $personality = Personality::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'statut' => 'required|string|in:Notable,Elite,Association,Entreprise',
                'contact' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'village_id' => 'required|exists:villages,id',
                'has_paid' => 'required|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation update personnalité : ', ['id' => $id, 'errors' => $validator->errors()->toArray()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->only(['name', 'statut', 'contact', 'description', 'village_id', 'has_paid']);
            if ($request->hasFile('image')) {
                CloudinaryHelper::delete($personality->image);
                Log::info('Ancienne image supprimée de Cloudinary', ['public_id' => $personality->image]);
                $publicId = CloudinaryHelper::upload($request->file('image'), 'personalities');
                $data['image'] = $publicId;
                Log::info('Nouvelle image uploadée sur Cloudinary', ['public_id' => $publicId]);
            }

            $personality->update($data);
            return redirect()->route('dashboard.personnalite.index')
                            ->with('success', 'Personnalité mise à jour avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée pour update : ', ['id' => $id]);
            return redirect()->route('dashboard.personnalite.index')
                           ->with('error', 'Personnalité non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur update personnalité : ', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.')->withInput();
        }
    }

    /**
     * Supprimer une personnalité.
     */
    public function destroy($id)
    {
        try {
            $personality = Personality::findOrFail($id);

            CloudinaryHelper::delete($personality->image);
            Log::info('Image supprimée de Cloudinary', ['public_id' => $personality->image]);

            $personality->delete();
            Log::info('Personnalité supprimée', ['id' => $id]);

            return redirect()->route('dashboard.personnalite.index')
                           ->with('success', 'Personnalité supprimée avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Personnalité non trouvée pour destroy : ', ['id' => $id]);
            return redirect()->route('dashboard.personnalite.index')
                           ->with('error', 'Personnalité non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur destroy personnalité : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.personnalite.index')
                           ->with('error', 'Erreur lors de la suppression.');
        }
    }
}