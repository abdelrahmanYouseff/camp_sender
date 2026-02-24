<?php

namespace App\Services\ClientDashboardService\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function user(): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->company_id === null || $user->role === 'super_admin') {
            return response()->json(['user' => null, 'company' => null], 401);
        }
        $company = Company::find($user->company_id);

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'role', 'company_id']),
            'company' => $company ? $company->only(['id', 'name', 'email']) : null,
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => [__('auth.failed')]]);
        }

        $user = Auth::user();
        if ($user->role === 'super_admin') {
            Auth::logout();
            throw ValidationException::withMessages(['email' => ['Only company users can access this area.']]);
        }
        if ($user->company_id === null) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => ['No company assigned.']]);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'OK',
            'user' => $user->only(['id', 'name', 'email', 'role', 'company_id']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'OK']);
    }
}
