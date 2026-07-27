<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'code',
    ];

    public function administrativeDivisionTypes()
    {
        return $this->hasMany(AdministrativeDivisionType::class)
                    ->orderBy('level', 'asc');
    }

    public function administrativeDivisions()
    {
        return $this->hasMany(AdministrativeDivision::class);
    }

    public function villages()
    {
        return Village::whereHas('villageGroup', function ($query) {
            $query->whereHas('parent', function ($q) {
                $q->where('country_id', $this->id);
            });
        })->get();
    }
}