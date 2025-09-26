<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use Spatie\Permission\Traits\HasRoles;

// class User extends Authenticatable
// {
//     use HasFactory, Notifiable;
//      use HasRoles;

//     protected $fillable = [
//         'name',
//         'email',
//         'password',
//         'mobile'
//     ];

//     protected $hidden = ['password'];
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $guard_name = 'web';  
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile'
    ];

    public function hasDirectPermission($permission): bool
    {
        $permission = $this->filterPermission($permission);
        if ($permission === null || !$this->permissions) {
            return false;
        }
        return $this->loadMissing('permissions')->permissions->contains($permission->getKey(), $permission->getKey());
    }

    protected $hidden = ['password'];
}