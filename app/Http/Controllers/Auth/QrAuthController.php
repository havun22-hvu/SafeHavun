<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\QrLoginToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QrAuthController extends Controller
{
    /**
     * Generate QR token for PC login
     */
    public function generate(Request $request): JsonResponse
    {
        $deviceInfo = [
            'browser' => $request->input('browser', 'Unknown'),
            'os' => $request->input('os', 'Unknown'),
            'ip' => $request->ip(),
        ];

        $token = QrLoginToken::generate($deviceInfo);

        return response()->json([
            'success' => true,
            'token' => $token->token,
            'expires_at' => $token->expires_at->toIso8601String(),
        ]);
    }

    /**
     * Check QR token status (polling from PC)
     */
    public function status(string $token): JsonResponse
    {
        $qrToken = QrLoginToken::findByToken($token);

        if (!$qrToken) {
            return response()->json([
                'status' => 'expired',
                'message' => 'QR code verlopen',
            ]);
        }

        if ($qrToken->isApproved()) {
            $user = $qrToken->user;
            $qrToken->markUsed();

            Auth::login($user);

            Log::info('QR LOGIN - Success', [
                'user_id' => $user->id,
                'token_id' => $qrToken->id,
            ]);

            return response()->json([
                'status' => 'approved',
                'redirect' => '/',
            ]);
        }

        return response()->json([
            'status' => $qrToken->status,
            'expires_in' => $qrToken->expires_at->diffInSeconds(now()),
        ]);
    }

    /**
     * Approve QR login from smartphone (requires auth)
     */
    public function approve(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string|size:64',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Je moet ingelogd zijn om te bevestigen.',
            ], 401);
        }

        $qrToken = QrLoginToken::findByToken($request->token);

        if (!$qrToken || !$qrToken->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'QR code ongeldig of verlopen.',
            ], 404);
        }

        $qrToken->approve($user);

        Log::info('QR LOGIN - Approved', [
            'user_id' => $user->id,
            'token_id' => $qrToken->id,
            'device_info' => $qrToken->device_info,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login bevestigd! Het andere apparaat wordt nu ingelogd.',
        ]);
    }

    /**
     * Scan page for PWA - shows camera to scan QR
     */
    public function scan()
    {
        return view('auth.qr-scan');
    }
}
