<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurposeDetail extends Model
{
    protected $table = 'purpose_details';
    protected $fillable = ['name'];
}