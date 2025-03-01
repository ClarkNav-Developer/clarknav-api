<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json(['message' => 'Email verification required.'], 403);
    }
}   