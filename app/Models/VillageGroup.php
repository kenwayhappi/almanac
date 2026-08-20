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
        'carousel_images',
    ];

    protected $casts = [
        'carousel_images' => 'array',
    ];

    protected $appends = ['image_url', 'chef_image_url', 'carousel_image_urls'];

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
        return $this->image ? \App\Helpers\CloudinaryHelper::url($this->image) : null;
    }

    public function getChefImageUrlAttribute()
    {
        return $this->chef_image ? \App\Helpers\CloudinaryHelper::url($this->chef_image) : null;
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
        if (empty($urls) && $this->image) {
            $urls[] = \App\Helpers\CloudinaryHelper::url($this->image);
        }
        return $urls;
    }
}
