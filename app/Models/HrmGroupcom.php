<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmGroupcom extends Model
{
    protected $table = 'hrm_groupcom';
    protected $fillable = ['name', 'core_company_id'];

    public function company()
    {
        return $this->belongsTo(CoreCompany::class, 'core_company_id');
    }
}