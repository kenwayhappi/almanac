<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activites';

    protected $fillable = [
        'name',
        'type',
        'description',
        'village_id',
        'image',
    ];

    protected $appends = ['image_url'];

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    // Accesseur pour retourner l'URL complète de l'image
    public function getImageUrlAttribute()
    {
        return $this->image ? url('storage/' . $this->image) : null;
    }
}