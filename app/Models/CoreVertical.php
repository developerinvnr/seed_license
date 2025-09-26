<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CoreVertical extends Model
{
    protected $table = 'core_vertical';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $fillable = ['api_id', 'vertical_name', 'vertical_code', 'effective_date', 'is_active'];
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
    ];
}