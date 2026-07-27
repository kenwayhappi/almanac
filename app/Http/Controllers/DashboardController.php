<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\VillageGroup;
use App\Models\Village;
use App\Models\Advertisement;
use App\Models\Personality;
use App\Models\Professional;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Core Count Metrics
        $countryCount = Country::count();
        $groupementCount = VillageGroup::count();
        $villageCount = Village::count();
        $advertisementCount = Advertisement::count();
        $totalAdViews = Advertisement::sum('views');
        $personalityCount = Personality::count();
        $professionalCount = Professional::count();
        $eventCount = Event::count();

        // Chart 1: Villages per Groupement (Top 8 groupements)
        $groupementsChartData = VillageGroup::withCount('villages')
            ->orderBy('villages_count', 'desc')
            ->take(8)
            ->get()
            ->map(function ($g) {
                return [
                    'label' => $g->name,
                    'count' => $g->villages_count,
                ];
            });

        // Chart 2: Ads distribution by Type
        $adsTypeData = Advertisement::select('type', DB::raw('count(*) as count'), DB::raw('sum(views) as total_views'))
            ->groupBy('type')
            ->get()
            ->map(function ($ad) {
                return [
                    'type' => ucfirst($ad->type ?? 'Autre'),
                    'count' => (int) $ad->count,
                    'views' => (int) ($ad->total_views ?? 0),
                ];
            });

        // Recent Villages & Ads
        $recentVillages = Village::with('villageGroup')->latest()->take(5)->get();
        $recentAds = Advertisement::latest()->take(5)->get();

        return view('dashboard.index', compact(
            'countryCount',
            'groupementCount',
            'villageCount',
            'advertisementCount',
            'totalAdViews',
            'personalityCount',
            'professionalCount',
            'eventCount',
            'groupementsChartData',
            'adsTypeData',
            'recentVillages',
            'recentAds'
        ));
    }
}