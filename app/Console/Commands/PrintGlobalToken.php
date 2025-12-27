<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class PrintGlobalToken extends Command
{
    protected $signature = 'token:print-global';
    protected $description = 'Generate and print an encrypted global token';

    public function handle()
    {
        // Step 1: Generate a random token
        $plainToken = base64_encode(random_bytes(32)); // 256-bit random token

        // Step 2: Generate timestamp
        // $timestamp ="2025-11-03 18:23:00";
        $timestamp = now(); // 1 year expiry

        // Step 3: Encrypt each value individually
        $data = [
            'authorization' => Crypt::encryptString($plainToken),
            'variable'      => Crypt::encryptString($timestamp),
            'expire_set'    => Crypt::encryptString($plainToken),
        ];

        // Step 4: Print encrypted JSON
        $this->line(json_encode($data, JSON_PRETTY_PRINT));

        // Optional: print plain data for reference
        $this->info("Plain token: {$plainToken}");
        $this->info("Plain timestamp: {$timestamp}");
    }

    // public function handle()
    // {
    //php artisan token:print-global------------
    //     // Step 1: Generate a random token (or a JWT string if you prefer)
    //     $plainToken = base64_encode(random_bytes(32)); // 256-bit random token

    //     // Step 2: Encrypt using Laravel's APP_KEY
    //     $encryptedToken = Crypt::encryptString($plainToken);

    //     // Step 3: Prepare JSON object
    //     $data = [
    //         'token' => $encryptedToken,
    //         'expires_at' => now()->addYear()->toDateTimeString(), // 1 year expiry
    //     ];

    //     // Step 4: Print JSON
    //     $this->line(json_encode($data, JSON_PRETTY_PRINT));

    //     // Optional: print plain token for your reference
    //     $this->info("Plain token (for testing): {$plainToken}");
    // }
}
