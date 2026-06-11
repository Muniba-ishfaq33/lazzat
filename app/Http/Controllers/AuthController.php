<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // POST /auth/register
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user, remember: true);

        return response()->json([
            'success' => true,
            'user'    => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ]);
    }

    // POST /auth/login
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']], remember: true)) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong email or password. Please try again.',
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'user'    => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ]);
    }

    // POST /auth/logout
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true]);
    }

    // GET /auth/me — returns current logged-in user
    public function me(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['authenticated' => false]);
        }

        return response()->json([
            'authenticated' => true,
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ]);
    }
}
