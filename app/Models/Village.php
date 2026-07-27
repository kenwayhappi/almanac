<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'histoire',
        'population',
        'village_image',
        'chief_image',
        'chef_village',
        'is_village',
        'village_group_id',
        'current_chief',
        'chief_description',
        'chief_achievements',
        'chief_interventions',
        'village_history',
        'historical_dynasty',
    ];

    public function villageGroup()
    {
        return $this->belongsTo(VillageGroup::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function personalities()
    {
        return $this->hasMany(Personality::class);
    }

    public function professionals()
    {
        return $this->hasMany(Professional::class);
    }

    protected $appends = ['village_image_url', 'chief_image_url'];

    public function getVillageImageUrlAttribute()
    {
        return $this->village_image ? url('storage/' . $this->village_image) : null;
    }

    public function getChiefImageUrlAttribute()
    {
        return $this->chief_image ? url('storage/' . $this->chief_image) : null;
    }
}
