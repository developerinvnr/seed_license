<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CoreVariety extends Model
{
    protected $table = 'core_variety';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $fillable = ['api_id', 'crop_id', 'variety_name', 'variety_code', 'numeric_code', 'category_id', 'is_active', 'effective_date'];
    protected $casts = [
        'id' => 'integer',
        'crop_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function crop()
    {
        return $this->belongsTo(CoreCrop::class, 'crop_id', 'id');
    }
}