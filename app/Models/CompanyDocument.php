<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDocument extends Model
{
    protected $fillable = [
        'company_id',
        'document_type',
        'file_path',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyMasterDetail::class, 'company_id', 'id');
    }
}