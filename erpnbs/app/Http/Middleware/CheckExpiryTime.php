<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use Spatie\Permission\Models\Role;

class CheckExpiryTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->expiry_time) {
                // Check if the expiry time is in the past
                if ($user->expiry_time->isPast()) {

                    // If old_type is available, update the user's type
                    if ($user->old_type != null) {
                        // Update the user's type and clear expiry time
                        $user->type = $user->old_type;
                        $user->expiry_time = null;
                        $user->save(); // Save the updated user type

                        // Find the role matching the new type
                        $role = Role::where('name', $user->type)->first();

                        // Sync the user's role
                        if ($role) {
                            $user->roles()->sync([$role->id]);
                        }

                    } else {
                        // If no old_type, log out the user
                        Auth::logout();

                        // Redirect with an expiration message
                        return redirect()->back()->with('status', __('Your account has expired. Please contact the administrator.'));
                    }
                }
            }

        }

        // Proceed to the next middleware or request handler
        return $next($request);
    }
}
