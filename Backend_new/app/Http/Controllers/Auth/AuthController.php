<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group 🔐 Authentification
 *
 * Gestion des tokens d'accès via Laravel Sanctum.
 */
class AuthController extends \App\Http\Controllers\Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Check if organisation exists
        $org = Organisation::findOrFail($validated['org_id']);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'org_id' => $validated['org_id'],
            'role' => $validated['role'] ?? 'field_agent',
        ]);

        $token = $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 86400,
        ], 201);
    }

    /**
     * Login - Step 1: Verify email and password
     * Returns temp_token if 2FA is enabled, or access_token if 2FA is disabled
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ], 401);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This account is inactive.'],
            ]);
        }

        // Log the login attempt
        $user->update(['last_login_at' => now()]);

        // If 2FA is disabled, return access token directly
        if (!$user->two_factor_enabled) {
            $token = $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ]);
        }

        // If 2FA is enabled, return temporary token
        $tempToken = $user->createToken('2fa-temp', [], now()->addMinutes(5))->plainTextToken;

        return response()->json([
            'message' => '2FA verification required',
            'temp_token' => $tempToken,
            'requires_2fa' => true,
            'user' => new UserResource($user),
        ], 202);
    }

    /**
     * Logout and revoke tokens
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Refresh access token using a refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        // For simplicity, we'll create a new token
        // In production, implement proper refresh token logic
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    /**
     * List all active sessions for the user
     */
    public function sessions(Request $request)
    {
        $user = $request->user();
        $sessions = $user->tokens()
            ->where('revoked', false)
            ->get(['id', 'name', 'last_used_at', 'created_at']);

        return response()->json([
            'sessions' => $sessions,
        ]);
    }

    /**
     * Revoke a specific session
     */
    public function revokeSession(Request $request, $sessionId): JsonResponse
    {
        $user = $request->user();
        $token = $user->tokens()->find($sessionId);

        if (!$token) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $token->delete();

        return response()->json(['message' => 'Session revoked']);
    }
}
