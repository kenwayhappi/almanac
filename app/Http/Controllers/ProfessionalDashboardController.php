<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use App\Models\Professional;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfessionalDashboardController extends Controller
{
    /**
     * Afficher la liste des artisans.
     */
    public function index(Request $request)
    {
        try {
            $query = Professional::with('village');
            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('profession', 'like', '%' . $request->search . '%');
            }
            $professionals = $query->paginate(9)->withQueryString();
            $villages = Village::all();
            Log::info('Artisans récupérés', ['count' => $professionals->count()]);
            return view('dashboard.professional.index', compact('professionals', 'villages'));
        } catch (\Exception $e) {
            Log::error('Erreur index artisans : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.professional.index')
                           ->with('error', 'Erreur lors du chargement des artisans.');
        }
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        try {
            $villages = Village::all();
            Log::info('Villages pour création artisan', ['count' => $villages->count()]);
            return view('dashboard.professional.create', compact('villages'));
        } catch (\Exception $e) {
            Log::error('Erreur create artisans : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.professional.index')
                           ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Enregistrer un nouvel artisan.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'profession' => 'required|string|in:Plombier,Électricien,Mécanicien,Menuisier,Peintre,Maçon',
                'contact' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'whatsapp' => 'nullable|string|max:20',
                'village_id' => 'required|exists:villages,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation store artisan : ', ['errors' => $validator->errors()->toArray()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->only(['name', 'profession', 'contact', 'email', 'whatsapp', 'village_id']);
            if ($request->hasFile('image')) {
                $publicId = CloudinaryHelper::upload($request->file('image'), 'professionals');
                $data['image'] = $publicId;
                Log::info('Image uploadée sur Cloudinary', ['public_id' => $publicId]);
            }

            Professional::create($data);
            return redirect()->route('dashboard.professional.index')
                            ->with('success', 'Artisan créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur store artisan : ', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création.')->withInput();
        }
    }

    /**
     * Afficher un artisan spécifique.
     */
    public function show($id)
    {
        try {
            $professional = Professional::with('village')->findOrFail($id);
            Log::info('Artisan récupéré', ['id' => $id]);
            return view('dashboard.professional.show', compact('professional'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Artisan non trouvé : ', ['id' => $id]);
            return redirect()->route('dashboard.professional.index')
                           ->with('error', 'Artisan non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur show artisan : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.professional.index')
                           ->with('error', 'Erreur lors du chargement de l\'artisan.');
        }
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit($id)
    {
        try {
            $professional = Professional::with('village')->findOrFail($id);
            $villages = Village::all();
            Log::info('Données pour modification artisan', ['professional_id' => $id, 'villages_count' => $villages->count()]);
            return view('dashboard.professional.edit', compact('professional', 'villages'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Artisan non trouvé pour edit : ', ['id' => $id]);
            return redirect()->route('dashboard.professional.index')
                           ->with('error', 'Artisan non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur edit artisan : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.professional.index')
                           ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Mettre à jour un artisan.
     */
    public function update(Request $request, $id)
    {
        try {
            $professional = Professional::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'profession' => 'required|string|in:Plombier,Électricien,Mécanicien,Menuisier,Peintre,Maçon',
                'contact' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'whatsapp' => 'nullable|string|max:20',
                'village_id' => 'required|exists:villages,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation update artisan : ', ['id' => $id, 'errors' => $validator->errors()->toArray()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->only(['name', 'profession', 'contact', 'email', 'whatsapp', 'village_id']);
            if ($request->hasFile('image')) {
                CloudinaryHelper::delete($professional->image);
                Log::info('Ancienne image supprimée de Cloudinary', ['public_id' => $professional->image]);
                $publicId = CloudinaryHelper::upload($request->file('image'), 'professionals');
                $data['image'] = $publicId;
                Log::info('Nouvelle image uploadée sur Cloudinary', ['public_id' => $publicId]);
            }

            $professional->update($data);
            return redirect()->route('dashboard.professional.index')
                            ->with('success', 'Artisan mis à jour avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Artisan non trouvé pour update : ', ['id' => $id]);
            return redirect()->route('dashboard.professional.index')
                           ->with('error', 'Artisan non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur update artisan : ', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.')->withInput();
        }
    }

    /**
     * Supprimer un artisan.
     */
    public function destroy($id)
    {
        try {
            $professional = Professional::findOrFail($id);

            CloudinaryHelper::delete($professional->image);
            Log::info('Image supprimée de Cloudinary', ['public_id' => $professional->image]);

            $professional->delete();
            Log::info('Artisan supprimé', ['id' => $id]);

            return redirect()->route('dashboard.professional.index')
                           ->with('success', 'Artisan supprimé avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Artisan non trouvé pour destroy : ', ['id' => $id]);
            return redirect()->route('dashboard.professional.index')
                           ->with('error', 'Artisan non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur destroy artisan : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.professional.index')
                           ->with('error', 'Erreur lors de la suppression.');
        }
    }
}