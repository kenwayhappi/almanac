<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrativeDivisionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'country_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function administrativeDivisions()
    {
        return $this->hasMany(AdministrativeDivision::class, 'type_id');
    }

    public function parentType()
    {
        return $this->country->administrativeDivisionTypes()
                             ->where('level', '<', $this->level)
                             ->orderBy('level', 'desc')
                             ->first();
    }

    public function childType()
    {
        return $this->country->administrativeDivisionTypes()
                             ->where('level', '>', $this->level)
                             ->orderBy('level', 'asc')
                             ->first();
    }
}