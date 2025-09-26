<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\LogOptions;
// use Spatie\Activitylog\Traits\LogsActivity;

// class License extends Model
// {
//     use HasFactory,LogsActivity;
//  protected $table = 'licenses'; 
//     protected $fillable = [
//         'license_type_id',
//         'license_name_id',
//         'registered_address',
//         'responsible_person',
//         'res_email',
//         'res_contact',
//         'res_department',
//         'res_designation',
//         'letter_date',
//         'date_of_issue',
//         'valid_upto',
//         'reminder_option',
//         'reminder_emails',
//         'reminder_days',
//         'lis_status',
//         'license_performance',
//     ];

//     public function licenseType()
//     {
//         return $this->belongsTo(LicenseType::class, 'license_type_id');
//     }

//     public function licenseName()
//     {
//         return $this->belongsTo(LicenseName::class, 'license_name_id');
//     }

//       public function getActivitylogOptions(): LogOptions
//     {
//         return LogOptions::defaults()
//         ->useLogName('licenses') 
//             ->logOnly([
//                 'license_type_id',
//                 'license_name_id',
//                 'registered_address',
//                 'responsible_person',
//                 'res_email',
//                 'res_contact',
//                 'res_department',
//                 'res_designation',
//                 'letter_date',
//                 'date_of_issue',
//                 'valid_upto',
//                 'reminder_option',
//                 'reminder_emails',
//                 'reminder_days',
//                 'lis_status',
//                 'license_performance'
//             ]);
//     }
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class License extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'licenses';

    protected $fillable = [
        'company_id',
        'groupcom_id',
        'license_type_id',
        'license_name_id',
        'state_id',
        'district_id',
        'city_village_id',
        'pincode',
        'responsible_person',
        'res_email',
        'res_contact',
        'res_department',
        'res_designation',
        'application_number',
        'letter_date',
        'date_of_issue',
        'registration_number',
        'certificate_number',
        'valid_upto',
        'reminder_option',
        'reminder_emails',
        'reminder_days',
        'lis_status',
        'license_performance',
        'license_creation_remark',
        'last_reminder_sent_at',
        'application_status',
    ];

    public function company()
    {
        return $this->belongsTo(CoreCompany::class, 'company_id');
    }

    public function groupcom()
    {
        return $this->belongsTo(HrmGroupcom::class, 'groupcom_id');
    }

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class, 'license_type_id');
    }

    public function licenseName()
    {
        return $this->belongsTo(LicenseName::class, 'license_name_id');
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

    public function documents()
    {
        return $this->hasMany(LicenseDocument::class, 'license_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('licenses')
            ->logOnly([
                'company_id',
                'license_type_id',
                'license_name_id',
                'state_id',
                'district_id',
                'city_village_id',
                'pincode',
                'responsible_person',
                'res_email',
                'res_contact',
                'res_department',
                'res_designation',
                'letter_date',
                'date_of_issue',
                'valid_upto',
                'reminder_option',
                'reminder_emails',
                'reminder_days',
                'lis_status',
                'license_performance',
                'last_reminder_sent_at',
                'application_status',
            ]);
    }
}