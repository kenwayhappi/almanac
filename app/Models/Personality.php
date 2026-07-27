<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personality extends Model
{
    use HasFactory;

    protected $table = 'personnalite';

    protected $fillable = [
        'name',
        'statut',
        'contact',
        'description',
        'village_id',
        'has_paid',
        'image',
    ];

    protected $casts = [
        'has_paid' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? url('storage/' . $this->image) : null;
    }
}