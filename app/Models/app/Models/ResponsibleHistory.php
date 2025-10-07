<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponsibleHistory extends Model
{
    protected $fillable = [
        'responsible_master_id',
        'certificate_no',
        'event',
        'description',
        'causer',
    ];
}