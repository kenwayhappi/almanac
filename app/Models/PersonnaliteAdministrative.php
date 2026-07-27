<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonnaliteAdministrative extends Model
{
    use HasFactory;

    protected $table = 'personnalites_administratives';

    protected $fillable = [
        'village_group_id',
        'nom',
        'prenom',
        'role',
        'biographie',
        'photo',
    ];

    protected $appends = ['photo_url'];

    public function villageGroup()
    {
        return $this->belongsTo(VillageGroup::class, 'village_group_id');
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? url('storage/' . $this->photo) : null;
    }
}