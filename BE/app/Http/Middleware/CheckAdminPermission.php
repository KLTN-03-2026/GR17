<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user instanceof Admin || !$user->hasPermission($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Ban khong co quyen thuc hien chuc nang nay.',
            ], 403);
        }

        return $next($request);
    }
}
