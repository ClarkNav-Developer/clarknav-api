<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;
use App\Models\ActivityHistory;
use App\Models\RefreshToken;
use Illuminate\Support\Str; // Add this line

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
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Generate a JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Set the token in a cookie
        $cookie = Cookie::make('token', $token, 60 * 24); // 1 day expiration

        // Return the token in the response
        return response()->json(['token' => $token], 201)->withCookie($cookie);

        // Return a success response without a token
        // return response()->json(['message' => 'User registered successfully'], 201);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login an existing user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="jwt_token"),
     *             @OA\Property(property="refresh_token", type="string", example="some_refresh_token"),
     *             @OA\Property(property="user", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        // Validate the incoming request data
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        // Attempt to authenticate the user
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Get the authenticated user
        $user = JWTAuth::user();
        $refreshToken = Str::random(60);
        RefreshToken::create(['user_id' => $user->id, 'token' => $refreshToken]);

        if ($request->remember_me) {
            $user->setRememberToken(Str::random(60));
            $user->save();
        }


        // Set the token and refresh token in cookies
        $tokenCookie = Cookie::make('token', $token, 60 * 24, null, null, false, true); // 1 day expiration, HTTP-only
        $refreshTokenCookie = Cookie::make('refresh_token', $refreshToken, 60 * 24 * 30, null, null, false, true); // 30 days expiration, HTTP-only

        // Return the token and user information in the response
        return response()->json([
            'token' => $token,
            'refresh_token' => $refreshToken,
            'user' => $user
        ], 200)->withCookie($tokenCookie)->withCookie($refreshTokenCookie);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout the authenticated user",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Failed to invalidate token"),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // Get the authenticated user
        $user = JWTAuth::parseToken()->authenticate();

        // Invalidate the token
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Failed to invalidate token'], 500);
        }

        // Remove the token and refresh token cookies
        $tokenCookie = Cookie::forget('token');
        $refreshTokenCookie = Cookie::forget('refresh_token');

        // Return a response
        return response()->json(['message' => 'Successfully logged out'])->withCookie($tokenCookie)->withCookie($refreshTokenCookie);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh JWT token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string", example="some_refresh_token")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="new_jwt_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid refresh token",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid refresh token")
     *         )
     *     )
     * )
     */

    public function refresh(Request $request)
    {
        $request->validate(['refresh_token' => 'required|string']);

        $refreshToken = RefreshToken::where('token', $request->refresh_token)->first();

        if (!$refreshToken) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        $token = JWTAuth::fromUser($refreshToken->user);

        // Set the new token in a cookie
        $cookie = Cookie::make('token', $token, 60 * 24); // 1 day expiration

        return response()->json(['token' => $token], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/some-user-action",
     *     summary="Log some user action",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"action"},
     *             @OA\Property(property="action", type="string", example="Some action")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Action logged successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Action logged")
     *         )
     *     )
     * )
     */
    public function someUserAction(Request $request)
    {
        $user = JWTAuth::user();
        ActivityHistory::create(['user_id' => $user->id, 'action' => 'Some action']);

        return response()->json(['message' => 'Action logged'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/get-identity",
     *     summary="Get the authenticated user's identity",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated user identity",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com")
     *             ),
     *             @OA\Property(property="succeeded", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
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
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","current_password","new_password","new_password_confirmation"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="current_password", type="string", format="password", example="oldPassword123"),
     *             @OA\Property(property="new_password", type="string", format="password", example="newPassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="newPassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User credentials updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User credentials updated successfully"),
     *             @OA\Property(property="succeeded", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
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