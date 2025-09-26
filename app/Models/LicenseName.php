<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseName extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_type_id',
        'license_name',
        'department_name',
        'state_id',
        'district_id',
        'city_village_id',
        'pincode',
        'fields'
    ];

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class, 'license_type_id');
    }

    public function state()
    {
        return $this->belongsTo(CoreState::class, 'state_id');
    }

    public function district()
    {
        return $this->belongsTo(CoreDistrict::class, 'district_id');
    }

    public function cityVillage()
    {
        return $this->belongsTo(CoreCityVillage::class, 'city_village_id');
    }

    public function licenses()
    {
        return $this->hasMany(License::class, 'license_name_id');
    }
}