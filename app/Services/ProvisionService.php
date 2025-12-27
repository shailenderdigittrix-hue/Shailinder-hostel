<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProvisionService
{
    public static function checkLogin()
    {
        if (!Auth::check()) {
            return;
        }

        try {
            $filePath = storage_path('app/tokens/global_token.json');
            if (!file_exists($filePath)) {
                Log::error('Global token file not found: ' . $filePath);
                return redirect('/login');
            }
            $data = json_decode(file_get_contents($filePath), true);
            $decryptedTimestamp = Crypt::decryptString($data['variable']);
            $expiryDate = Carbon::parse($decryptedTimestamp, 'Asia/Kolkata');
            $now = Carbon::now('Asia/Kolkata');
            if ($expiryDate->isPast()) {
                Auth::logout();
                Session::invalidate();
                Session::regenerateToken();
                Log::warning("User {$userId} logged out due to expired token.");
                Session::flash('error', 'Your Authentication has been expired, Please contact Digittrix support for further assistance!');
                abort(redirect()->route('login'));
            }
            
        } catch (\Exception $e) {
            $userId = Auth::id();
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
            Log::error("ProvisionService error for user {$userId}: " . $e->getMessage());
            abort(redirect()->route('login'));
        }
    }


}
