<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreState extends Model
{
    use HasFactory;

    protected $table = 'core_state';
    protected $fillable = ['country_id', 'state_name'];

    public function districts()
    {
        return $this->hasMany(CoreDistrict::class, 'state_id');
    }
}