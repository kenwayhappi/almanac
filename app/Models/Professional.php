<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    use HasFactory;

    protected $table = 'professionals';

    protected $fillable = [
        'name',
        'profession',
        'contact',
        'email',
        'whatsapp',
        'village_id',
        'image',
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