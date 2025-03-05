<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('CheckAdmin middleware executed');
        if (Auth::check() && Auth::user()->isAdmin) {
            Log::info('User is admin');
            return $next($request);
        }

        Log::warning('Unauthorized access attempt');
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}