<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Models\SmtpDetail;

class LoadSmtpConfig
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check if DB is connected before accessing it
            DB::connection()->getPdo();

            if (Schema::hasTable('smtp_settings')) {
                $smtp = SmtpDetail::first(); // fetch the first row safely
                if ($smtp) {
                    Config::set('mail.mailers.smtp.transport', 'smtp');
                    Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
                    Config::set('mail.mailers.smtp.port', 587);
                    Config::set('mail.mailers.smtp.encryption', 'tls');
                    Config::set('mail.mailers.smtp.username', $smtp->username);
                    Config::set('mail.mailers.smtp.password', $smtp->password);
                    Config::set('mail.from.address', $smtp->username);
                    Config::set('mail.from.name', $smtp->from_name);
                }
            }
        } catch (\Exception $e) {
            Log::warning('SMTP configuration could not be loaded: ' . $e->getMessage());
        }

        return $next($request);
    }

    
}
