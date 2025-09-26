<?php


namespace App\Traits;

use Spatie\Permission\Traits\HasPermissions as BaseHasPermissions;

trait CustomHasPermissions
{
    use BaseHasPermissions;

    /**
     * Check if the model has the given permission.
     *
     * @param mixed $permission
     * @param string|null $guardName
     * @return bool
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {

        
        // Prevent calling contains() on null
        if ($this->permissions === null) {
            return false;
        }

        return parent::hasPermissionTo($permission, $guardName);
    }
}