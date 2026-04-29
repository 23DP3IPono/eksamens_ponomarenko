<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || $user->loma !== 'Admins') {
            return response()->json(['message' => 'Nav administratora tiesību'], 403);
        }
        return $next($request);
    }
}