<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use App\Models\VillageGroup;
use App\Models\Country;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Récupérer les groupements avec leur division parent
            $groupements = VillageGroup::with('parent')->get();

            // Récupérer les pays pour correspondance
            $countries = Country::pluck('name', 'id')->toArray();

            // Regrouper les groupements par pays
            $groupementsByCountry = $groupements->groupBy(function ($groupement) use ($countries) {
                $countryId = $groupement->parent->country_id ?? 'unknown';
                return $countries[$countryId] ?? 'Inconnu';
            })->map(function ($group) {
                return $group->shuffle()->take(3); // Mélanger et limiter à 3 par pays
            })->take(4); // Prendre 4 pays

            // Récupérer les publicités (vidéo, photo, PDF, texte) pour la position 'accueil'
            $advertisements = Advertisement::where('position', 'accueil')
                ->whereIn('type', ['video', 'photo', 'pdf', 'text'])
                ->get()
                ->map(function ($ad) {
                    if (!empty($ad->file_path)) {
                        $ad->file_url = CloudinaryHelper::url($ad->file_path, $ad->type);
                    }
                    return $ad;
                });

            $initialAds = $advertisements->shuffle()->take(4)->values();

            // Counts for hero stats
            $totalVillages = \App\Models\Village::count();
            $totalGroupements = $groupements->count();
            $totalPersonalities = \App\Models\Personality::count();

            return view('accueil', compact('groupementsByCountry', 'advertisements', 'initialAds', 'totalVillages', 'totalGroupements', 'totalPersonalities'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la page d\'accueil', ['message' => $e->getMessage()]);
            return view('accueil', [
                'groupementsByCountry' => collect([]),
                'advertisements' => collect([]),
                'initialAds' => collect([]),
            ])->with('error', 'Erreur lors du chargement de la page.');
        }
    }
}