<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CoreCrop extends Model
{
    protected $table = 'core_crop';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $fillable = ['api_id', 'vertical_id', 'crop_name', 'crop_code', 'numeric_code', 'effective_date', 'is_active'];
    protected $casts = [
        'id' => 'integer',
        'vertical_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function vertical()
    {
        return $this->belongsTo(CoreVertical::class, 'vertical_id', 'id');
    }

    public function varieties()
    {
        return $this->hasMany(CoreVariety::class, 'crop_id', 'id');
    }
}