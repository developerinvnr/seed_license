<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponsibleMastersLicenseDetails extends Model
{
    use HasFactory;

    protected $table = 'responsible_masters_license_details';

    protected $fillable = [
        'certificate_no',
        'responsible_master_id',
        'license_type_id',
        'license_name_id',
    ];

    public function responsibleMaster()
    {
        return $this->belongsTo(ResponsibleMaster::class, 'responsible_master_id');
    }

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class, 'license_type_id');
    }

    public function licenseName()
    {
        return $this->belongsTo(LicenseName::class, 'license_name_id');
    }
}