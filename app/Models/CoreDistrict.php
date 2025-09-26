<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreDistrict extends Model
{
    use HasFactory;

    protected $table = 'core_district';
    protected $fillable = ['state_id', 'district_name'];

    public function state()
    {
        return $this->belongsTo(CoreState::class, 'state_id');
    }

    public function cityVillages()
    {
        return $this->hasMany(CoreCityVillage::class, 'district_id');
    }
}