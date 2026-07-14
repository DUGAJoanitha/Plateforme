<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends \App\Http\Controllers\Controller
{
    /**
     * Enable 2FA for the authenticated user
     * Returns QR code URL for setting up TOTP
     */
    public function enableTwoFactor(Request $request): JsonResponse
    {
        $user = $request->user();

        // Generate secret
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        // Store secret temporarily in cache (expires in 15 minutes)
        Cache::put('2fa_secret_' . $user->id, $secret, now()->addMinutes(15));

        // Generate QR code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'message' => 'Scan this QR code with your authenticator app',
            'qr_url' => $qrCodeUrl,
            'secret' => $secret,
            'setup_expires_at' => now()->addMinutes(15),
        ]);
    }

    /**
     * Verify and confirm 2FA setup
     * User must provide the code from their authenticator app
     */
    public function verifyTwoFactorSetup(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();
        $secret = Cache::get('2fa_secret_' . $user->id);

        if (!$secret) {
            return response()->json([
                'error' => '2FA setup has expired. Please try again.',
            ], 422);
        }

        // Verify the code
        $google2fa = new Google2FA();
        if (!$google2fa->verifyKey($secret, $request->code)) {
            return response()->json([
                'error' => 'Invalid verification code.',
            ], 422);
        }

        // Save the secret (encrypted)
        $user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        // Clear cache
        Cache::forget('2fa_secret_' . $user->id);

        return response()->json([
            'message' => '2FA has been enabled successfully',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Verify 2FA code during login
     * User provides the temporary token from login and the OTP code
     */
    public function verify2FACode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json([
                'error' => '2FA is not enabled for this account',
            ], 422);
        }

        // Check if code was already used (replay attack prevention)
        if (Cache::has('2fa_used_' . $user->id . '_' . $request->code)) {
            return response()->json([
                'error' => 'This code has already been used',
            ], 422);
        }

        // Verify the code
        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return response()->json([
                'error' => 'Invalid verification code',
            ], 422);
        }

        // Mark code as used (expires in 30 seconds)
        Cache::put('2fa_used_' . $user->id . '_' . $request->code, true, now()->addSeconds(30));

        // Create new access token
        $token = $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken;

        return response()->json([
            'message' => 'Authentication successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Disable 2FA
     * User must provide password and current 2FA code for verification
     */
    public function disableTwoFactor(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        // Verify password
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Password is incorrect',
            ], 422);
        }

        // Verify current 2FA code
        if ($user->two_factor_enabled) {
            $google2fa = new Google2FA();
            $secret = decrypt($user->two_factor_secret);

            if (!$google2fa->verifyKey($secret, $request->code)) {
                return response()->json([
                    'error' => 'Invalid verification code',
                ], 422);
            }
        }

        // Disable 2FA
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
        ]);

        return response()->json([
            'message' => '2FA has been disabled',
        ]);
    }

    /**
     * Check 2FA status
     */
    public function check2FAStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'two_factor_enabled' => $user->two_factor_enabled,
            'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
        ]);
    }
}
