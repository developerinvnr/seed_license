<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseDocument extends Model
{
    use HasFactory;

    protected $table = 'license_documents';

    protected $fillable = [
        'license_id',
        'document_type',
        'file_path',
        'file_name',
        'document_name',
    ];

    public function license()
    {
        return $this->belongsTo(License::class, 'license_id');
    }
}