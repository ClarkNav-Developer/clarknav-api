<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticatedSessionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login user",
     *     description="Logs in a user and returns no content",
     *     operationId="loginUser",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Header(
     *         header="X-CSRF-TOKEN",
     *         description="CSRF token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     )
     * )
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $user = $request->user();

        $user->tokens()->delete();

        $token = $user->createToken('api-token');

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }


    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout user",
     *     description="Logs out the authenticated user and returns no content",
     *     operationId="logoutUser",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=204,
     *         description="Successful operation"
     *     )
     * )
     */

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        // $request->session()->regenerateToken();

        // Clear the cookies using the Cookie facade
        $response = response()->json(['message' => 'Logged out successfully'], 200);

        // Forget XSRF-TOKEN cookie
        $response->headers->setCookie(Cookie::forget('XSRF-TOKEN', '/', config('session.domain')));

        // Forget laravel_session cookie
        $response->headers->setCookie(Cookie::forget('laravel_session', '/', config('session.domain')));

        return $response;
    }
}