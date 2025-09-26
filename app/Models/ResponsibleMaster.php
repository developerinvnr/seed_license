<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ResponsibleMaster extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'responsible_masters';
    protected $fillable = [
        'certificate_no',
        'core_company_id',
        'core_employee_id',
        'emp_code',
        'Authorised_Through',
        'Scope_of_Authorisation',
        'Authorisation_Issued_By',
        'Issue_Date',
        'Effective_From',
        'Valid_up_to',
        'Authorization_status',
        'auth_doc',
        'purpose_details',
        'Revocation_Date',
        'revocation_doc',
        'auth_certificate',
    ];

    protected $casts = [];

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class, 'license_type');
    }

    public function licenseName()
    {
        return $this->belongsTo(LicenseName::class, 'license_name');
    }

    public function licenseDetails()
    {
        return $this->hasMany(ResponsibleMastersLicenseDetails::class, 'responsible_master_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $fields = [
                    'Authorised_Through' => 'Authorized Through',
                    'Scope_of_Authorisation' => 'Scope of Authorization',
                    'Authorisation_Issued_By' => 'Authorization Issued By',
                    'purpose_details' => 'Authorized Purpose',
                    'Issue_Date' => 'Issue Date',
                    'Effective_From' => 'Effective From',
                    'Valid_up_to' => 'Valid Up to',
                    'Authorization_status' => 'Authorization Status',
                    'auth_doc' => 'Authorization Document',
                    'Revocation_Date' => 'Revocation Date',
                    'revocation_doc' => 'Revocation Document',
                ];

                $changes = [];
                foreach ($this->getChanges() as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        $oldValue = $this->getOriginal($key) ?? 'N/A';
                        $newValue = $value ?? 'N/A';
                        $changes[] = "{$fields[$key]} changed from '$oldValue' to '$newValue'";
                    }
                }

                $description = count($changes) > 0 ? implode(', ', $changes) : 'No fields changed';
                return ucfirst($eventName) . ": " . $description;
            });
    }

    public function company()
    {
        return $this->belongsTo(CoreCompany::class, 'core_company_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(CoreEmployee::class, 'core_employee_id', 'id');
    }

    public function getPurposeNamesAttribute()
    {
        if (!$this->purpose_details) {
            return [];
        }

        $ids = explode(',', $this->purpose_details);
        return \App\Models\PurposeDetail::whereIn('id', $ids)->pluck('name')->toArray();
    }
}
