<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreCityVillage extends Model
{
    use HasFactory;

    protected $table = 'core_city_village';
    protected $fillable = ['district_id', 'city_village_name','pincode'];

    public function district()
    {
        return $this->belongsTo(CoreDistrict::class, 'district_id');
    }
}