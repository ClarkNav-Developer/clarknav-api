<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get all users",
     *     description="Retrieve a list of all users. Only accessible by admin.",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::all();
        return response()->json($users);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user by ID",
     *     description="Retrieve a user by their ID. Only accessible by admin or the user themselves.",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update user",
     *     description="Update a user's information. Only accessible by admin or the user themselves.",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="firstname", type="string"),
     *             @OA\Property(property="lastname", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="isAdmin", type="boolean"),
     *             @OA\Property(property="isUser", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rules = [
            'firstname' => ['sometimes', 'string', 'max:255'],
            'lastname' => ['sometimes', 'string', 'max:255'],
            'password' => ['sometimes', 'confirmed', Rules\Password::defaults()],
        ];

        if (auth()->user()->isAdmin()) {
            $rules['email'] = ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id];
            $rules['isAdmin'] = ['sometimes', 'boolean'];
            $rules['isUser'] = ['sometimes', 'boolean'];
        }

        $request->validate($rules);

        $data = $request->only('firstname', 'lastname', 'password');
        if (auth()->user()->isAdmin()) {
            $data = array_merge($data, $request->only('email', 'isAdmin', 'isUser'));
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete user",
     *     description="Delete a user by their ID. Only accessible by admin or the user themselves.",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/user-role",
     *     summary="Get user role",
     *     description="Retrieve the roles of the authenticated user.",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="isAdmin", type="boolean"),
     *             @OA\Property(property="isUser", type="boolean")
     *         )
     *     )
     * )
     */
    public function getUserRole()
    {
        $user = auth()->user();
        return response()->json([
            'isAdmin' => $user->isAdmin(),
            'isUser' => $user->isUser(),
        ]);
    }
}