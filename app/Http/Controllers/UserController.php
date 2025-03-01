<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($user);
    }

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

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function getUserRole()
    {
        $user = auth()->user();
        return response()->json([
            'isAdmin' => $user->isAdmin(),
            'isUser' => $user->isUser(),
        ]);
    }
}