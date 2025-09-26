<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CropMaster extends Model
{
    protected $table = 'crop_master';
    protected $fillable = ['crop_vertical_id', 'crop_id', 'crop_variety_id'];

    public function vertical()
    {
        return $this->belongsTo(CoreVertical::class, 'crop_vertical_id', 'id');
    }

    public function crop()
    {
        return $this->belongsTo(CoreCrop::class, 'crop_id', 'id');
    }

    public function variety()
    {
        return $this->belongsTo(CoreVariety::class, 'crop_variety_id', 'id');
    }
}