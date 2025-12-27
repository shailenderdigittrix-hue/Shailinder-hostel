<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VerifyGlobalToken
{
    public function handle($request, Closure $next)
    {
        $filePath = storage_path('app/tokens/global_token.json'); 
        if (!file_exists($filePath)) {
            Log::error('Global token file not found: ' . $filePath);
            return redirect('/login');
        }
        $data = json_decode(file_get_contents($filePath), true);
        if (!isset($data['authorization'], $data['variable'])) {
            Log::error('Invalid token file structure', ['data' => $data]);
            return redirect('/login');
        }
        $decryptedTimestamp = Crypt::decryptString($data['variable']);
        $expiresAt = Carbon::parse($decryptedTimestamp, 'Asia/Kolkata');
        $now = Carbon::now('Asia/Kolkata');
        if ($expiresAt->isPast()) {
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
            Log::info("Authentication may have expired:---1", ['expiresAt' => $expiresAt]);
            Session::flash('error', 'Your Authentication has been expired, Please contact Digittrix support for further assistance!');
            return response()->view('auth.login', [], 404);
            // abort(redirect()->route('login'));
        }
        return $next($request);
    }
}
