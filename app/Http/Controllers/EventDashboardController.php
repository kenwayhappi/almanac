<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use App\Models\Event;
use App\Models\Village;
use App\Models\Country;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class EventDashboardController extends Controller
{
    /**
     * Liste des événements
     */
    public function index(Request $request)
    {
        try {
            $query = Event::with('village');
            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            $events = $query->paginate(9)->withQueryString();
            $villages = Village::all();
            Log::info('Événements récupérés', ['count' => $events->count()]);
            return view('dashboard.events.index', compact('events', 'villages'));
        } catch (\Exception $e) {
            Log::error('Erreur index événements : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Erreur lors du chargement des événements.');
        }
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        try {
            $countries = Country::all();
            $villages = Village::with(['villageGroup.parent.country'])->get()->map(function ($village) {
                return [
                    'id' => $village->id,
                    'name' => $village->name,
                    'country_id' => $village->villageGroup->parent->country_id ?? null,
                ];
            });
            Log::info('Données pour création événement', [
                'countries_count' => $countries->count(),
                'villages_count' => $villages->count()
            ]);
            return view('dashboard.events.create', compact('countries', 'villages'));
        } catch (\Exception $e) {
            Log::error('Erreur create événements : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Enregistrer un nouvel événement
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'type' => 'required|string|in:Festival,Cérémonie,Marché,Autre',
                'description' => 'nullable|string',
                'village_id' => 'required|integer|exists:villages,id',
                'country_id' => 'required|integer|exists:countries,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'image' => 'nullable|image|max:10240',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation store événement : ', ['errors' => $validator->errors()->toArray()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->only(['name', 'type', 'description', 'village_id', 'start_date', 'end_date']);
            if ($request->hasFile('image')) {
                $publicId = CloudinaryHelper::upload($request->file('image'), 'events');
                $data['image'] = $publicId;
                Log::info('Image uploadée sur Cloudinary', ['public_id' => $publicId]);
            }

            Event::create($data);
            return redirect()->route('dashboard.events.index')
                            ->with('success', 'Événement créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur store événement : ', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création.')->withInput();
        }
    }

    /**
     * Afficher un événement
     */
    public function show($id)
    {
        try {
            $event = Event::with('village')->findOrFail($id);
            $contributions = Contribution::where('event_id', $id)->paginate(10);
            Log::info('Événement récupéré avec contributions', ['id' => $id, 'contributions_count' => $contributions->count()]);
            return view('dashboard.events.show', compact('event', 'contributions'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Événement non trouvé : ', ['id' => $id]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur show événement : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Erreur lors du chargement de l’événement.');
        }
    }

    /**
     * Récupérer les contributions pour un événement (AJAX)
     */
    public function getContributions(Request $request, $eventId)
    {
        Log::debug('getContributions called', [
            'event_id' => $eventId,
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'input' => $request->all()
        ]);
        try {
            // Vérifier si l'événement existe
            $event = Event::find($eventId);
            if (!$event) {
                Log::warning('Événement non trouvé pour contributions', ['event_id' => $eventId]);
                return response()->json(['error' => 'Événement non trouvé.'], 404);
            }

            // Récupérer les contributions
            $contributions = Contribution::where('event_id', $eventId)
                ->select('id', 'name', 'contributor_type', 'amount')
                ->get()
                ->map(function ($contribution) {
                    // Assurer que amount est un nombre
                    $contribution->amount = (float) $contribution->amount;
                    return $contribution;
                });

            $totalAmount = $contributions->sum('amount');

            Log::info('Contributions récupérées pour événement', [
                'event_id' => $eventId,
                'contributions_count' => $contributions->count(),
                'contributions' => $contributions->toArray(),
                'total_amount' => $totalAmount
            ]);

            return response()->json([
                'contributions' => $contributions,
                'total_amount' => $totalAmount
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur récupération contributions : ', [
                'event_id' => $eventId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Erreur lors du chargement des contributions.'], 500);
        }
    }

    /**
     * Formulaire de modification
     */
    public function edit($id)
    {
        try {
            $event = Event::with('village')->findOrFail($id);
            $countries = Country::all();
            $villages = Village::with(['villageGroup.parent.country'])->get()->map(function ($village) {
                return [
                    'id' => $village->id,
                    'name' => $village->name,
                    'country_id' => $village->villageGroup->parent->country_id ?? null,
                ];
            });
            Log::info('Données pour modification événement', [
                'event_id' => $id,
                'countries_count' => $countries->count(),
                'villages_count' => $villages->count()
            ]);
            return view('dashboard.events.edit', compact('event', 'countries', 'villages'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Événement non trouvé pour edit : ', ['id' => $id]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur edit événement : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'type' => 'required|string|in:Festival,Cérémonie,Marché,Autre',
                'description' => 'nullable|string',
                'village_id' => 'required|integer|exists:villages,id',
                'country_id' => 'required|integer|exists:countries,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'image' => 'nullable|image|max:10240',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation update événement : ', ['id' => $id, 'errors' => $validator->errors()->toArray()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->only(['name', 'type', 'description', 'village_id', 'start_date', 'end_date']);
            if ($request->hasFile('image')) {
                CloudinaryHelper::delete($event->image);
                Log::info('Ancienne image supprimée de Cloudinary', ['public_id' => $event->image]);
                $publicId = CloudinaryHelper::upload($request->file('image'), 'events');
                $data['image'] = $publicId;
                Log::info('Nouvelle image uploadée sur Cloudinary', ['public_id' => $publicId]);
            }

            $event->update($data);
            return redirect()->route('dashboard.events.index')
                            ->with('success', 'Événement mis à jour avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Événement non trouvé pour update : ', ['id' => $id]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur update événement : ', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.')->withInput();
        }
    }

    /**
     * Supprimer un événement
     */
    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            $contributionsCount = $event->contributions()->count();
            Log::info('Suppression événement, contributions associées', [
                'event_id' => $id,
                'contributions_count' => $contributionsCount
            ]);

            // Supprimer explicitement les contributions associées
            $event->contributions()->delete();

            // Supprimer l'image de l'événement si elle existe
            CloudinaryHelper::delete($event->image);
            Log::info('Image événement supprimée de Cloudinary', ['public_id' => $event->image]);

            // Supprimer l'événement
            $event->delete();

            Log::info('Événement supprimé avec contributions', [
                'event_id' => $id,
                'contributions_deleted' => $contributionsCount
            ]);
            return redirect()->route('dashboard.events.index')
                           ->with('success', 'Événement et ses contributions supprimés avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Événement non trouvé pour destroy : ', ['event_id' => $id]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur destroy événement : ', ['event_id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * Ajouter une contribution
     */
    public function storeContribution(Request $request, $eventId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'contributor_type' => 'required|in:person,association',
                'name' => 'required|string|max:100',
                'amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation ajout contribution : ', ['errors' => $validator->errors()->toArray()]);
                return redirect()->route('dashboard.events.show', $eventId)
                                ->withErrors($validator)
                                ->withInput();
            }

            Contribution::create([
                'event_id' => $eventId,
                'contributor_type' => $request->contributor_type,
                'name' => $request->name,
                'amount' => $request->amount,
            ]);

            Log::info('Contribution ajoutée', ['event_id' => $eventId, 'name' => $request->name, 'amount' => $request->amount]);
            return redirect()->route('dashboard.events.show', $eventId)
                            ->with('success', 'Contribution ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur ajout contribution : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Erreur lors de l’ajout de la contribution.');
        }
    }

    /**
     * Afficher le formulaire de modification d'une contribution
     */
    public function editContribution($eventId, $contributionId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $contribution = Contribution::where('event_id', $eventId)->findOrFail($contributionId);
            Log::info('Formulaire de modification contribution chargé', [
                'event_id' => $eventId,
                'contribution_id' => $contributionId
            ]);
            return view('dashboard.events.contribution_edit', compact('event', 'contribution'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Contribution ou événement non trouvé pour edit : ', [
                'event_id' => $eventId,
                'contribution_id' => $contributionId
            ]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Contribution ou événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur edit contribution : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    /**
     * Mettre à jour une contribution
     */
    public function updateContribution(Request $request, $eventId, $contributionId)
    {
        try {
            $contribution = Contribution::where('event_id', $eventId)->findOrFail($contributionId);

            $validator = Validator::make($request->all(), [
                'contributor_type' => 'required|in:person,association',
                'name' => 'required|string|max:100',
                'amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation update contribution : ', [
                    'event_id' => $eventId,
                    'contribution_id' => $contributionId,
                    'errors' => $validator->errors()->toArray()
                ]);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $contribution->update([
                'contributor_type' => $request->contributor_type,
                'name' => $request->name,
                'amount' => $request->amount,
            ]);

            Log::info('Contribution mise à jour', [
                'event_id' => $eventId,
                'contribution_id' => $contributionId,
                'name' => $request->name,
                'amount' => $request->amount
            ]);
            return redirect()->route('dashboard.events.show', $eventId)
                            ->with('success', 'Contribution mise à jour avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Contribution ou événement non trouvé pour update : ', [
                'event_id' => $eventId,
                'contribution_id' => $contributionId
            ]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Contribution ou événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur update contribution : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Erreur lors de la mise à jour de la contribution.');
        }
    }

    /**
     * Supprimer une contribution
     */
    public function destroyContribution($eventId, $contributionId)
    {
        try {
            $contribution = Contribution::where('event_id', $eventId)->findOrFail($contributionId);
            $contribution->delete();
            Log::info('Contribution supprimée', [
                'event_id' => $eventId,
                'contribution_id' => $contributionId
            ]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('success', 'Contribution supprimée avec succès.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Contribution ou événement non trouvé pour destroy : ', [
                'event_id' => $eventId,
                'contribution_id' => $contributionId
            ]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Contribution ou événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur destroy contribution : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Erreur lors de la suppression de la contribution.');
        }
    }

    /**
     * Filtrer les contributions par plage de montants et type de contributeur
     */
    public function filterContributions(Request $request, $eventId)
    {
        try {
            $event = Event::with('village')->findOrFail($eventId);
            $validator = Validator::make($request->all(), [
                'contributor_type' => 'nullable|in:person,association',
                'min_amount' => 'nullable|numeric|min:0',
                'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            ]);

            if ($validator->fails()) {
                Log::warning('Erreur validation filtre contributions : ', ['errors' => $validator->errors()->toArray()]);
                return redirect()->route('dashboard.events.show', $eventId)
                                ->withErrors($validator)
                                ->withInput();
            }

            $query = Contribution::where('event_id', $eventId);
            if ($request->filled('contributor_type')) {
                $query->where('contributor_type', $request->contributor_type);
            }
            if ($request->filled('min_amount')) {
                $query->where('amount', '>=', $request->min_amount);
            }
            if ($request->filled('max_amount')) {
                $query->where('amount', '<=', $request->max_amount);
            }

            $contributions = $query->paginate(10);
            Log::info('Contributions filtrées', [
                'event_id' => $eventId,
                'filters' => $request->only(['contributor_type', 'min_amount', 'max_amount']),
                'contributions_count' => $contributions->count()
            ]);

            return view('dashboard.events.show', compact('event', 'contributions'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Événement non trouvé pour filtre contributions : ', ['event_id' => $eventId]);
            return redirect()->route('dashboard.events.index')
                           ->with('error', 'Événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur filtre contributions : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Erreur lors du filtrage des contributions.');
        }
    }

    /**
     * Télécharger les contributions en PDF
     */
    public function downloadContributionsPdf(Request $request, $eventId)
    {
        try {
            $event = Event::with('village')->findOrFail($eventId);
            $query = Contribution::where('event_id', $eventId);
            if ($request->filled('contributor_type')) {
                $query->where('contributor_type', $request->contributor_type);
            }
            if ($request->filled('min_amount')) {
                $query->where('amount', '>=', $request->min_amount);
            }
            if ($request->filled('max_amount')) {
                $query->where('amount', '<=', $request->max_amount);
            }

            $contributions = $query->get();
            $totalAmount = $contributions->sum('amount');

            Log::info('Génération PDF contributions', [
                'event_id' => $eventId,
                'contributions_count' => $contributions->count(),
                'total_amount' => $totalAmount
            ]);

            $pdf = Pdf::loadView('dashboard.events.contributions_pdf', compact('event', 'contributions', 'totalAmount'));
            return $pdf->download('contributions_event_' . $eventId . '.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Événement non trouvé pour PDF : ', ['event_id' => $eventId]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur génération PDF : ', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.events.show', $eventId)
                           ->with('error', 'Erreur lors de la génération du PDF.');
        }
    }
    // Méthode pour récupérer les contributeurs publiquement
    public function getPublicContributions(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $contributions = Contribution::where('event_id', $eventId)->get();
            $totalAmount = $contributions->sum('amount');

            Log::info('Contributions publiques récupérées', [
                'event_id' => $eventId,
                'contributions_count' => $contributions->count(),
                'total_amount' => $totalAmount,
            ]);

            return response()->json([
                'contributions' => $contributions->map(function ($contribution) {
                    return [
                        'name' => $contribution->name,
                        'contributor_type' => $contribution->contributor_type,
                        'amount' => $contribution->amount,
                    ];
                }),
                'total_amount' => $totalAmount,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Événement non trouvé pour contributions publiques', ['event_id' => $eventId]);
            return response()->json(['error' => 'Événement non trouvé'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des contributions publiques', [
                'event_id' => $eventId,
                'message' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    // Méthode pour télécharger le PDF des contribute devotees publiquement
    public function downloadPublicContributionsPdf(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $contributions = Contribution::where('event_id', $eventId)->get();
            $totalAmount = $contributions->sum('amount');

            $pdf = Pdf::loadView('dashboard.events.contributions_pdf', [
                'event' => $event,
                'contributions' => $contributions,
                'totalAmount' => $totalAmount,
            ]);

            Log::info('PDF des contributions publiques généré', [
                'event_id' => $eventId,
                'contributions_count' => $contributions->count(),
            ]);

            return $pdf->download('contributions_event_' . $eventId . '.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Événement non trouvé pour PDF contributions publiques', ['event_id' => $eventId]);
            return redirect()->back()->with('error', 'Événement non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du PDF contributions publiques', [
                'event_id' => $eventId,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la génération du PDF.');
        }
    }
}
