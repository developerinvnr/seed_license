<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CompanyMasterPersonDetail extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'designation',
        'din',
        'pan',
        'aadhaar',
        'contact_number',
        'email',
        'appointment_date',
        'resignation_date',
        'aadhar_doc',
        'pan_doc',
        'passport_doc',
        'driving_license_doc',
        'bank_passbook_doc',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyMasterDetail::class, 'company_id', 'company_id');
    }
}