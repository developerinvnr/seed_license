<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreApi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'api_id',
        'api_name',
        'api_end_point',
        'description',
        'parameters',
        'table_name',
    ];
}
