<?php

namespace App\Http\Middleware;

use App\Discord\Interaction;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyDiscordSignature
{
    private string $publicKey;

    public function __construct()
    {
        $this->publicKey = hex2bin(config('discord.public_key'));
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $signature = hex2bin($request->header('X-Signature-Ed25519'));
        $message = $request->header('X-Signature-Timestamp') . $request->getContent();

        try {
            if (sodium_crypto_sign_verify_detached($signature, $message, $this->publicKey)) {
                if ($request->input('type') === 1) {
                    return response()->json(['type' => 1], 200);
                }

                return $next($request);
            }
        } catch (\SodiumException) {
        }

        return response()->json(['message' => 'Invalid signature'], 401);
    }
}
