<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryDashboardController;
use App\Http\Controllers\GroupementDashboardController;
use App\Http\Controllers\VillageDashboardController;
use App\Http\Controllers\ActivityDashboardController;
use App\Http\Controllers\EventDashboardController;
use App\Http\Controllers\PersonalityDashboardController;
use App\Http\Controllers\ProfessionalDashboardController;
use App\Http\Controllers\AdvertisementDashboardController;
use App\Http\Controllers\PersonnaliteAdministrativeController;

/*
|--------------------------------------------------------------------------
| Routes Web Almanac Cameroun
|--------------------------------------------------------------------------
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('accueil');

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Pages statiques
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/a-propos', function () {
    return view('a-propos');
})->name('a-propos');

// Recherche et listes
Route::get('/recherche', [VillageDashboardController::class, 'search'])->name('recherche');
Route::get('/liste', function () {
    return redirect()->route('recherche', ['searchType' => 'villages']);
})->name('liste.list');
Route::get('/groupements', function () {
    return redirect()->route('recherche', ['searchType' => 'groupements']);
})->name('liste.groupements');

// Publicité tracking
Route::post('/publicite/{id}/track-view', [App\Http\Controllers\Api\AdvertisementController::class, 'trackView'])->name('publicite.trackView');

// Division Cascade API
Route::get('/api/divisions/country/{countryId}', [App\Http\Controllers\Api\DivisionController::class, 'getRegionsByCountry'])->name('api.divisions.country');
Route::get('/api/divisions/parent/{parentId}', [App\Http\Controllers\Api\DivisionController::class, 'getChildrenByParent'])->name('api.divisions.parent');
Route::get('/api/divisions/groupements/{divisionId}', [App\Http\Controllers\Api\DivisionController::class, 'getGroupementsByDivision'])->name('api.divisions.groupements');

// Routes publiques Fiches (Page Unique Monopage)
Route::prefix('village')->group(function () {
    Route::get('/{id}', [VillageDashboardController::class, 'publicShow'])->name('village.show');
    Route::get('/{id}/decouvrir', fn($id) => redirect()->route('village.show', $id))->name('village.decouvrir');
    Route::get('/{id}/personnalite', fn($id) => redirect()->route('village.show', $id))->name('village.personnalite');
    Route::get('/{id}/artisant', fn($id) => redirect()->route('village.show', $id))->name('village.artisant');
    Route::get('/{id}/ensavoirplus', fn($id) => redirect()->route('village.show', $id))->name('village.ensavoirplus');
    Route::get('/events/{eventId}/contributions', [EventDashboardController::class, 'getPublicContributions'])->name('village.events.contributions');
    Route::get('/events/{eventId}/contributions/pdf', [EventDashboardController::class, 'downloadPublicContributionsPdf'])->name('village.events.contributions.pdf');
});

// Groupements publics (Page Unique Monopage)
Route::prefix('groupement')->group(function () {
    Route::get('/{id}', [GroupementDashboardController::class, 'publicShow'])->name('groupement.show');
    Route::get('/{villageGroupId}/personnalites-administratives', fn($id) => redirect()->route('groupement.show', $id))->name('groupement.personnaliteAdministrative');
});


/*
|--------------------------------------------------------------------------
| Routes du Tableau de Bord Admin (SPA & Modales CRUD)
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard')->name('dashboard.')->middleware('auth')->group(function () {
    // Vue d'ensemble
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Profil Admin
    Route::get('/profil', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profil', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Pays & Divisions
    Route::prefix('pays')->group(function () {
        Route::get('/', [CountryDashboardController::class, 'index'])->name('pays.index');
        Route::post('/', [CountryDashboardController::class, 'store'])->name('pays.store');
        Route::put('/{id}', [CountryDashboardController::class, 'update'])->name('pays.update');
        Route::delete('/{id}', [CountryDashboardController::class, 'destroy'])->name('pays.destroy');
        Route::get('/create', fn() => redirect()->route('dashboard.pays.index'))->name('pays.create');
        Route::get('/{id}', fn() => redirect()->route('dashboard.pays.index'))->name('pays.show');
        Route::get('/{id}/edit', fn() => redirect()->route('dashboard.pays.index'))->name('pays.edit');

        // Modales CRUD Divisions Administratives
        Route::post('/divisions', [CountryDashboardController::class, 'storeDivision'])->name('pays.divisions.store');
        Route::put('/divisions/{id}', [CountryDashboardController::class, 'updateDivision'])->name('pays.divisions.update');
        Route::delete('/divisions/{id}', [CountryDashboardController::class, 'destroyDivision'])->name('pays.divisions.destroy');
    });

    // Groupements
    Route::prefix('groupements')->group(function () {
        Route::get('/', [GroupementDashboardController::class, 'index'])->name('groupements.index');
        Route::post('/', [GroupementDashboardController::class, 'store'])->name('groupements.store');
        Route::put('/{id}', [GroupementDashboardController::class, 'update'])->name('groupements.update');
        Route::delete('/{id}', [GroupementDashboardController::class, 'destroy'])->name('groupements.destroy');
        Route::get('/create', fn() => redirect()->route('dashboard.groupements.index'))->name('groupements.create');
        Route::get('/{id}', fn() => redirect()->route('dashboard.groupements.index'))->name('groupements.show');
        Route::get('/{id}/edit', fn() => redirect()->route('dashboard.groupements.index'))->name('groupements.edit');
    });

    // Villages
    Route::prefix('villages')->group(function () {
        Route::get('/', [VillageDashboardController::class, 'index'])->name('villages.index');
        Route::post('/', [VillageDashboardController::class, 'store'])->name('villages.store');
        Route::put('/{id}', [VillageDashboardController::class, 'update'])->name('villages.update');
        Route::delete('/{id}', [VillageDashboardController::class, 'destroy'])->name('villages.destroy');
        Route::get('/create', fn() => redirect()->route('dashboard.villages.index'))->name('villages.create');
        Route::get('/{id}', fn() => redirect()->route('dashboard.villages.index'))->name('villages.show');
        Route::get('/{id}/edit', fn() => redirect()->route('dashboard.villages.index'))->name('villages.edit');
    });

    // Activités
    Route::prefix('activites')->group(function () {
        Route::get('/', [ActivityDashboardController::class, 'index'])->name('activites.index');
        Route::post('/', [ActivityDashboardController::class, 'store'])->name('activites.store');
        Route::put('/{id}', [ActivityDashboardController::class, 'update'])->name('activites.update');
        Route::delete('/{id}', [ActivityDashboardController::class, 'destroy'])->name('activites.destroy');
        Route::get('/create', fn() => redirect()->route('dashboard.activites.index'))->name('activites.create');
        Route::get('/{id}', fn() => redirect()->route('dashboard.activites.index'))->name('activites.show');
        Route::get('/{id}/edit', fn() => redirect()->route('dashboard.activites.index'))->name('activites.edit');
    });

    // Personnalités administratives
    Route::prefix('personnalites-administratives')->group(function () {
        Route::get('/', [PersonnaliteAdministrativeController::class, 'index'])->name('personnalites_administratives.index');
        Route::post('/', [PersonnaliteAdministrativeController::class, 'store'])->name('personnalites_administratives.store');
        Route::put('/{id}', [PersonnaliteAdministrativeController::class, 'update'])->name('personnalites_administratives.update');
        Route::delete('/{id}', [PersonnaliteAdministrativeController::class, 'destroy'])->name('personnalites_administratives.destroy');
        Route::get('/create', fn() => redirect()->route('dashboard.personnalites_administratives.index'))->name('personnalites_administratives.create');
        Route::get('/{id}', fn() => redirect()->route('dashboard.personnalites_administratives.index'))->name('personnalites_administratives.show');
        Route::get('/{id}/edit', fn() => redirect()->route('dashboard.personnalites_administratives.index'))->name('personnalites_administratives.edit');
    });

    // Publicités
    Route::prefix('advertisements')->group(function () {
        Route::get('/', [AdvertisementDashboardController::class, 'index'])->name('advertisements.index');
        Route::post('/', [AdvertisementDashboardController::class, 'store'])->name('advertisements.store');
        Route::put('/{id}', [AdvertisementDashboardController::class, 'update'])->name('advertisements.update');
        Route::delete('/{id}', [AdvertisementDashboardController::class, 'destroy'])->name('advertisements.destroy');
        Route::get('/create', fn() => redirect()->route('dashboard.advertisements.index'))->name('advertisements.create');
        Route::get('/{id}', fn() => redirect()->route('dashboard.advertisements.index'))->name('advertisements.show');
        Route::get('/{id}/edit', fn() => redirect()->route('dashboard.advertisements.index'))->name('advertisements.edit');
    });

    // Événements
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventDashboardController::class, 'index'])->name('index');
        Route::post('/', [EventDashboardController::class, 'store'])->name('store');
        Route::put('/{id}', [EventDashboardController::class, 'update'])->name('update');
        Route::delete('/{id}', [EventDashboardController::class, 'destroy'])->name('destroy');
        Route::get('/create', fn() => redirect()->route('dashboard.events.index'))->name('create');
        Route::get('/{id}', fn() => redirect()->route('dashboard.events.index'))->name('show');
        Route::get('/{id}/edit', fn() => redirect()->route('dashboard.events.index'))->name('edit');
    });

    // Personnalités
    Route::prefix('personnalite')->group(function () {
        Route::get('/', [PersonalityDashboardController::class, 'index'])->name('personnalite.index');
        Route::post('/', [PersonalityDashboardController::class, 'store'])->name('personnalite.store');
        Route::put('/{id}', [PersonalityDashboardController::class, 'update'])->name('personnalite.update');
        Route::delete('/{id}', [PersonalityDashboardController::class, 'destroy'])->name('personnalite.destroy');
        Route::get('/create', fn() => redirect()->route('dashboard.personnalite.index'))->name('personnalite.create');
        Route::get('/{id}', fn() => redirect()->route('dashboard.personnalite.index'))->name('personnalite.show');
        Route::get('/{id}/edit', fn() => redirect()->route('dashboard.personnalite.index'))->name('personnalite.edit');
    });

    // Professionnels
    Route::prefix('professional')->group(function () {
        Route::get('/', [ProfessionalDashboardController::class, 'index'])->name('professional.index');
        Route::post('/', [ProfessionalDashboardController::class, 'store'])->name('professional.store');
        Route::put('/{id}', [ProfessionalDashboardController::class, 'update'])->name('professional.update');
        Route::delete('/{id}', [ProfessionalDashboardController::class, 'destroy'])->name('professional.destroy');
        Route::get('/create', fn() => redirect()->route('dashboard.professional.index'))->name('professional.create');
        Route::get('/{id}', fn() => redirect()->route('dashboard.professional.index'))->name('professional.show');
        Route::get('/{id}/edit', fn() => redirect()->route('dashboard.professional.index'))->name('professional.edit');
    });
});
