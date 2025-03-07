<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user() && auth()->user()->isAdmin) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}