<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class CheckGlobalToken
{
    public function handle($request, Closure $next)
    {
        $file = 'tokens/global_token.json';

        if (!Storage::exists($file)) {
            return response()->json(['message' => 'Global token not found.'], 401);
        }

        $data = json_decode(Storage::get($file), true);

        if (Carbon::parse($data['expires_at'])->isPast()) {
            return response()->json(['message' => 'Global token expired.'], 401);
        }

        $tokenFromFile = $data['token'];
        $tokenFromHeader = $request->bearerToken();

        if ($tokenFromHeader !== $tokenFromFile) {
            return response()->json(['message' => 'Invalid token.'], 401);
        }

        return $next($request);
    }
}
