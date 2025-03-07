<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user() && auth()->user()->isUser) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}