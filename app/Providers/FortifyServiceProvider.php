<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Encryption\DecryptException;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

use Illuminate\Auth\Events\Login;
use App\Services\ProvisionService;


class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind your custom LoginResponse
        $this->app->singleton(
            LoginResponseContract::class,
            LoginResponse::class
        );
    }

    public function boot(): void
    {
        // --- Step 1: Check token during login ---
        Fortify::authenticateUsing(function ($request) {

            // Call your ProvisionService
            \App\Services\ProvisionService::checkLogin();

            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                return $user;
            }
        });
    }
        
    // public function boot(): void
    // {
    //     Fortify::authenticateUsing(function (Request $request) {

    //         // --- Step 1: Check account lock ---
    //         try {
    //             $encrypted = Storage::get('tokens/global_token.json');
    //             if (empty($encrypted)) {
    //                 Log::error('global_token.json file is empty.', [
    //                     'encrypted file data' => $encrypted,
    //                 ]);
    //                 throw new \Exception('Account lock configuration is empty.');
    //             }
    //             Log::error('global_token.json file is empty.', [
    //                 'encrypted file data' => $encrypted,
    //                 // 'ip' => $request->ip(),
    //             ]);
    //             // Decrypt safely
    //             try {
    //                 $config = json_decode(Crypt::decryptString($encrypted), true);
    //             } catch (DecryptException $e) {
    //                 Log::error('Failed to decrypt global_token.json.', [
    //                     'email' => $request->email,
    //                     // 'ip' => $request->ip(),
    //                     'exception_message' => $e->getMessage(),
    //                     'stack_trace' => $e->getTraceAsString(),
    //                 ]);
    //                 throw new \Exception('Account lock decryption failed.');
    //             }

    //             // Validate config structure
    //             if (!isset($config['start_date'], $config['duration_days'])) {
    //                 Log::error('Invalid global_token.json structure.', [
    //                     'email' => $request->email,
    //                     // 'ip' => $request->ip(),
    //                 ]);
    //                 throw new \Exception('Invalid account lock configuration.');
    //             }

    //             $startDate = Carbon::parse($config['start_date']);
    //             $expiryDate = $startDate->copy()->addDays($config['duration_days']);

    //             if (Carbon::now()->greaterThan($expiryDate)) {
    //                 Session::flash('error', 'Logins are currently disabled. Please contact the administrator.');

    //                 Log::warning('Login attempt blocked due to expired account lock.', [
    //                     'email' => $request->email,
    //                     'expiry_date' => $expiryDate->toDateTimeString(),
    //                     // 'ip' => $request->ip(),
    //                 ]);

    //                 throw ValidationException::withMessages([
    //                     'email' => 'Logins are disabled for all users. Contact administrator.',
    //                 ]);
    //             }

    //         } catch (\Exception $e) {
    //             Session::flash('error', 'Login system configuration error. Contact administrator.');

    //             Log::error('Login system configuration error.', [
    //                 'email' => $request->email,
    //                 'exception_message' => $e->getMessage(),
    //                 'exception_code' => $e->getCode(),
    //                 'stack_trace' => $e->getTraceAsString(),
    //                 // 'ip' => $request->ip(),
    //             ]);

    //             throw ValidationException::withMessages([
    //                 'email' => 'Login system configuration error. Contact administrator.',
    //             ]);
    //         }

    //         // --- Step 2: Authenticate user normally ---
    //         $user = \App\Models\User::where('email', $request->email)->first();

    //         if ($user && Hash::check($request->password, $user->password)) {
    //             Log::info('User logged in successfully.', [
    //                 'user_id' => $user->id,
    //                 'email' => $user->email,
    //                 // 'ip' => $request->ip(),
    //             ]);

    //             return $user;
    //         }

    //         Log::warning('Failed login attempt.', [
    //             'email' => $request->email,
    //             // 'ip' => $request->ip(),
    //         ]);

    //         throw ValidationException::withMessages([
    //             'email' => __('These credentials do not match our records.'),
    //         ]);
    //     });
    // }
}
