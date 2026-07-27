<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\AdministrativeDivisionTypeController;
use App\Http\Controllers\Api\AdministrativeDivisionController;
use App\Http\Controllers\Api\VillageGroupController;
use App\Http\Controllers\Api\VillageController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PersonalityController;
use App\Http\Controllers\Api\ProfessionalController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\PersonnaliteAdministrativeController;

Route::prefix('v1')->group(function () {
    // Pays
    Route::apiResource('countries', CountryController::class);
    Route::get('countries/{id}/hierarchy', [CountryController::class, 'hierarchy']);

    // Types de divisions administratives
    Route::apiResource('administrative-division-types', AdministrativeDivisionTypeController::class);
    Route::get('administrative-division-types/country/{countryId}', [AdministrativeDivisionTypeController::class, 'getTypesByCountry']);

    // Divisions administratives
    Route::apiResource('administrative-divisions', AdministrativeDivisionController::class);
    Route::get('administrative-divisions/country/{countryId}', [AdministrativeDivisionController::class, 'getDivisionsByCountry']);
    Route::get('administrative-divisions/{id}/children', [AdministrativeDivisionController::class, 'getChildren']);
    Route::get('administrative-divisions/{id}/villages', [AdministrativeDivisionController::class, 'getVillages']);

    // Groupes de villages
    Route::apiResource('village-groups', VillageGroupController::class);
    Route::get('village-groups/country/{countryId}', [VillageGroupController::class, 'getGroupsByCountry']);
    Route::get('village-groups/{id}/villages', [VillageGroupController::class, 'getVillages']);
    Route::get('village-groups/{id}/personnalites-administratives', [PersonnaliteAdministrativeController::class, 'showVillageGroupPersonnalites']);

    // Villages
    Route::apiResource('villages', VillageController::class);
    Route::get('villages/division/{divisionId}', [VillageController::class, 'getVillagesByDivision']);
    Route::get('villages/group/{groupId}', [VillageController::class, 'getVillagesByGroup']);
    Route::post('villages/search', [VillageController::class, 'search']);

    // Activités
    Route::apiResource('activities', ActivityController::class);

    // Événements
    Route::apiResource('events', EventController::class);

    // Personnalités
    Route::apiResource('personalities', PersonalityController::class);

    // Professionnels
    Route::apiResource('professionals', ProfessionalController::class);

    // Publicités
    Route::apiResource('advertisements', AdvertisementController::class);

    // Personnalités administratives
    Route::apiResource('personnalites-administratives', PersonnaliteAdministrativeController::class);
});