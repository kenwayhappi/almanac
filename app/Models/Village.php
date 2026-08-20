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
        'carousel_images',
    ];

    protected $casts = [
        'carousel_images' => 'array',
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

    protected $appends = ['village_image_url', 'chief_image_url', 'carousel_image_urls'];

    public function getVillageImageUrlAttribute()
    {
        return $this->village_image ? \App\Helpers\CloudinaryHelper::url($this->village_image) : null;
    }

    public function getChiefImageUrlAttribute()
    {
        return $this->chief_image ? \App\Helpers\CloudinaryHelper::url($this->chief_image) : null;
    }

    public function getCarouselImageUrlsAttribute()
    {
        $urls = [];
        if (!empty($this->carousel_images) && is_array($this->carousel_images)) {
            foreach ($this->carousel_images as $img) {
                if ($img) {
                    $urls[] = \App\Helpers\CloudinaryHelper::url($img);
                }
            }
        }
        if (empty($urls) && $this->village_image) {
            $urls[] = \App\Helpers\CloudinaryHelper::url($this->village_image);
        }
        return $urls;
    }
}
