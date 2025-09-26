<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class SuperAdminBypass
{
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $user = $request->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has Super Admin role
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Check permission if specified
        if ($permission) {
            try {
                if (!$user->hasPermissionTo($permission)) {
                    return redirect()->route('dashboard');
                }
            } catch (\Exception $e) {
                // Log the error and redirect to dashboard
                \Log::error('Permission check failed: ' . $e->getMessage());
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
