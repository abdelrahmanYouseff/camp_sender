<?php

namespace App\Services\ClientDashboardService\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $employees = User::query()
            ->where('company_id', $companyId)
            ->get(['id', 'name', 'email', 'role', 'status', 'created_at'])
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'status' => $u->status,
                'created_at' => $u->created_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $employees]);
    }

    public function store(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:company_admin,employee,agent'],
        ]);

        $validated['company_id'] = $companyId;
        $validated['password'] = bcrypt($validated['password']);
        $validated['status'] = 'active';
        $user = User::create($validated);

        return response()->json(['message' => 'Created', 'data' => $user->only(['id', 'name', 'email', 'role', 'status'])], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $user = User::query()->where('company_id', $companyId)->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['sometimes', 'string', 'in:company_admin,employee,agent'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }
        $user->update($validated);

        return response()->json(['message' => 'Updated', 'data' => $user->fresh()->only(['id', 'name', 'email', 'role', 'status'])]);
    }

    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $user = User::query()->where('company_id', $companyId)->findOrFail($id);

        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);

        return response()->json(['message' => 'Updated', 'data' => ['id' => $user->id, 'status' => $user->fresh()->status]]);
    }
}
