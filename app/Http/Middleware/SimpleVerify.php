<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpleVerify
{
    const ACCESS_ROLE = ['admin', 'staff'];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // access_token = admin/staff + personalConfigToken
        if (!Auth::check()) {
            $accessToken = $request->associated_session;

            if (!$accessToken) {
                abort(404);
            }

            $role = strtolower(substr($accessToken, 0, 5));

            if (!in_array($role, self::ACCESS_ROLE)) {
                abort(404);
            }

            // $canAccess = $accessToken === env('APP_ADMIN_ACCESS_TOKEN') || $accessToken === env('APP_STAFF_ACCESS_TOKEN');

            // Auto login if verified by access token

            // Hard code
            $email = 'admin@admin.com';
            if ($role === 'staff') {
                $email = 'staff@linhgv.com';
            }

            $password = substr($accessToken, 5);

            $login = [
                'email' => $email,
                'password' => $password,
            ];


            if (!Auth::attempt($login))
            {
                abort(404);
            }
        }

        return $next($request);
    }
}
