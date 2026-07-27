<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdministrativeDivision extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_id',
        'parent_id',
        'country_id',
    ];

    public function type()
    {
        return $this->belongsTo(AdministrativeDivisionType::class, 'type_id');
    }

    public function parent()
    {
        return $this->belongsTo(AdministrativeDivision::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AdministrativeDivision::class, 'parent_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function villageGroups()
    {
        return $this->hasMany(VillageGroup::class, 'parent_id');
    }

    public function getAllVillages()
    {
        return Village::whereIn('village_group_id', $this->villageGroups()->pluck('id'))->get();
    }
}
