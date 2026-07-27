<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillageGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'chef_groupement',
        'histoire',
        'parent_id',
        'chef_image',
        'image',
    ];

    protected $appends = ['image_url', 'chef_image_url'];

    public function parent()
    {
        return $this->belongsTo(AdministrativeDivision::class, 'parent_id');
    }

    public function country()
    {
        return $this->parent ? $this->parent->country() : null;
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'village_group_id');
    }

    public function personnalitesAdministratives()
    {
        return $this->hasMany(PersonnaliteAdministrative::class, 'village_group_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? url('storage/' . $this->image) : null;
    }

    public function getChefImageUrlAttribute()
    {
        return $this->chef_image ? url('storage/' . $this->chef_image) : null;
    }
}
