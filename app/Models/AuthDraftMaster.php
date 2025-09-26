<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthDraftMaster extends Model
{
   use HasFactory;

    protected $fillable = ['title', 'content', 'input_fields'];

    protected $casts = [
        'input_fields' => 'array', // Cast JSON to array
    ];
}
