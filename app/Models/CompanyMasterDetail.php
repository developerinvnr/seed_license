<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyMasterDetail extends Model
{
    protected $fillable = [
        'company_id', 
        'company_name',
        'company_code',
        'registration_number',
        'gst_number',
    ];

    public function documents()
    {
        return $this->hasMany(CompanyDocument::class, 'company_id', 'id');
    }

    public function directors()
    {
        return $this->hasMany(CompanyMasterPersonDetail::class, 'company_id', 'company_id');
    }

    public function getCompanyIdAttribute($value)
    {
        return $value ?: 'com' . $this->id;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->company_id) {
                $model->company_id = 'com' . $model->id; 
            }
        });

        static::created(function ($model) {
            $model->company_id = 'com' . $model->id;
            $model->save();
        });
    }
}