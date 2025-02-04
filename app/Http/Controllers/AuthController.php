<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;
use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="refresh_token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = JWTAuth::fromUser($user);
        $refreshToken = Str::random(60);
        $hashedRefreshToken = Hash::make($refreshToken);

        // Expire old refresh tokens
        RefreshToken::where('user_id', $user->id)->delete();

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $hashedRefreshToken,
            'expires_at' => now()->addDays(30),
        ]);

        $tokenCookie = Cookie::make('token', $token, 60 * 24, null, null, false, true);
        $refreshTokenCookie = Cookie::make('refresh_token', $refreshToken, 60 * 24 * 30, null, null, false, true);

        return response()->json(['token' => $token, 'refresh_token' => $refreshToken], 201)
            ->withCookie($tokenCookie)
            ->withCookie($refreshTokenCookie);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login user and issue JWT and refresh token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="refresh_token", type="string"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = JWTAuth::user();
        $refreshToken = Str::random(60);
        $hashedRefreshToken = Hash::make($refreshToken);

        // Expire old refresh tokens
        RefreshToken::where('user_id', $user->id)->delete();

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $hashedRefreshToken,
            'expires_at' => now()->addDays(30),
        ]);

        $tokenCookie = Cookie::make('token', $token, 60 * 24, null, null, false, true);
        $refreshTokenCookie = Cookie::make('refresh_token', $refreshToken, 60 * 24 * 30, null, null, false, true);

        return response()->json([
            'token' => $token,
            'refresh_token' => $refreshToken,
            'user' => $user
        ], 200)->withCookie($tokenCookie)->withCookie($refreshTokenCookie);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout user and delete refresh token",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to invalidate token",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();

            // Invalidate the JWT token
            JWTAuth::invalidate($token);

            // Delete refresh token
            $user = JWTAuth::user();
            if ($user) {
                RefreshToken::where('user_id', $user->id)->delete();
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Failed to invalidate token'], 500);
        }

        $tokenCookie = Cookie::forget('token');
        $refreshTokenCookie = Cookie::forget('refresh_token');

        return response()->json(['message' => 'Successfully logged out'])
            ->withCookie($tokenCookie)
            ->withCookie($refreshTokenCookie);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh JWT token using a valid refresh token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string", example="your_refresh_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid refresh token",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function refresh(Request $request)
    {
        $request->validate(['refresh_token' => 'required|string']);

        $user = JWTAuth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $refreshToken = RefreshToken::where('user_id', $user->id)->first();

        if (!$refreshToken || !Hash::check($request->refresh_token, $refreshToken->token)) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        $newToken = JWTAuth::fromUser($user);

        $tokenCookie = Cookie::make('token', $newToken, 60 * 24, null, null, false, true);

        return response()->json(['token' => $newToken], 200)->withCookie($tokenCookie);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/get-identity",
     *     summary="Get authenticated user's identity",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="User identity retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="succeeded", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function getIdentity(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json(['data' => $user, 'succeeded' => true], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/auth/update-credentials/{id}",
     *     summary="Update user credentials",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","current_password"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="current_password", type="string", format="password", example="currentpassword123"),
     *             @OA\Property(property="new_password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User credentials updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="succeeded", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Current password is incorrect",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function updateCredentials(Request $request, $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'current_password' => 'required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);

        // Check if the authenticated user is the same as the user being updated
        if ($user->id !== JWTAuth::parseToken()->authenticate()->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Update user details
        $user->first_name = $validatedData['first_name'];
        $user->last_name = $validatedData['last_name'];

        // Change password if provided
        if (!empty($validatedData['new_password'])) {
            // Check if the current password is correct
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return response()->json(['error' => 'Current password is incorrect'], 400);
            }
            $user->password = Hash::make($validatedData['new_password']);
        }

        $user->save();

        return response()->json(['message' => 'User credentials updated successfully', 'succeeded' => true], 200);
    }
}