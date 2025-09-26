<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    use HasFactory;
    protected $fillable = ['license_type', 'fields'];

      public function licenses()
    {
        return $this->hasMany(License::class, 'license_type_id');
    }

}
