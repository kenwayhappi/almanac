<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeDivision;
use App\Models\VillageGroup;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Get Level 1 Divisions (Regions) for a Country
     */
    public function getRegionsByCountry($countryId)
    {
        $regions = AdministrativeDivision::where('country_id', $countryId)
            ->whereNull('parent_id')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'divisions' => $regions
        ]);
    }

    /**
     * Get Level 2 Divisions (Departments) under a Region
     */
    public function getChildrenByParent($parentId)
    {
        $children = AdministrativeDivision::where('parent_id', $parentId)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'divisions' => $children
        ]);
    }

    /**
     * Get Groupements under a Division
     */
    public function getGroupementsByDivision($divisionId)
    {
        $groupements = VillageGroup::where('parent_id', $divisionId)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        // If no groupements directly attached to this division, return all groupements in the country/region
        if ($groupements->isEmpty()) {
            $division = AdministrativeDivision::find($divisionId);
            if ($division) {
                $childIds = AdministrativeDivision::where('parent_id', $divisionId)->pluck('id');
                $groupements = VillageGroup::whereIn('parent_id', $childIds)
                    ->select('id', 'name')
                    ->orderBy('name', 'asc')
                    ->get();
            }
        }

        return response()->json([
            'success' => true,
            'groupements' => $groupements
        ]);
    }
}
