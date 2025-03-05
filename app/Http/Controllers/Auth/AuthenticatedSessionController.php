<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login",
     *     description="Handle an incoming authentication request.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="kpjaculbia@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Yc6l310-31")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful."),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Please verify your email address before logging in.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Please verify your email address before logging in.")
     *         )
     *     )
     * )
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        // Remove email verification check
        // if (!$request->user()->hasVerifiedEmail()) {
        //     Auth::logout(); // Log out the user
        //     return response()->json([
        //         'message' => 'Please verify your email address before logging in.',
        //     ], 403);
        // }

        $request->session()->regenerate();

        // Issue a token for API authentication
        $token = $request->user()->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => $request->user(),
            'token' => $token,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout",
     *     description="Destroy an authenticated session.",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successful.")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }
}