<?php

namespace App\Services\OwnerDashboard\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form (handled by SPA; this endpoint for redirect/response).
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }
        return response()->json(['message' => 'Login required']);
    }

    /**
     * Attempt admin login (super_admin only).
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $user = Auth::user();
        if ($user->role !== 'super_admin') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => [__('Only super admin can access this area.')],
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'OK',
            'user' => $user->only(['id', 'name', 'email', 'role']),
        ]);
    }

    /**
     * Logout admin.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'OK']);
    }

    /**
     * Get current admin user (for SPA init).
     */
    public function user(): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'super_admin') {
            return response()->json(['user' => null], 401);
        }

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'role']),
        ]);
    }
}
