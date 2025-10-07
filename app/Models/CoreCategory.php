<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreCategory extends Model
{
    protected $table = 'core_category';
    protected $primaryKey = 'id';
    protected $fillable = ['api_id', 'category_name', 'category_code', 'numeric_code', 'effective_date', 'is_active'];
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function varieties()
    {
        return $this->hasMany(CoreVariety::class, 'category_id', 'id');
    }
}