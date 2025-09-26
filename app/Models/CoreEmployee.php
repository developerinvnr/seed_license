<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreEmployee extends Model
{
    protected $table = 'core_employee';

    protected $fillable = [
        'employee_id',
        'emp_code',
        'emp_name',
        'emp_email',
        'emp_department',
        'emp_designation',
        'emp_reporting'
    ];

    public function responsibles()
    {
        return $this->hasMany(ResponsibleMaster::class, 'core_employee_id', 'id');
    }

    public function manager()
    {
        return $this->belongsTo(self::class, 'emp_reporting', 'id');
    }
}
