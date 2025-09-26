<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'state';

    protected $fillable = [
        'country_id',
        'state_name',
        'state_code',
        'short_code',
        'effective_date',
        'is_active',
    ];
}
