<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginViewResponse;
use App\Http\Responses\LoginViewResponse as CustomLoginViewResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Services\ProvisionService;

use App\Models\SmtpDetail;
use Illuminate\Support\Facades\Config;




class AppServiceProvider extends ServiceProvider
{
    // function decryptIfNeeded($value) {
    //     try {
    //         return \Illuminate\Support\Facades\Crypt::decryptString($value);
    //     } catch (\Exception $e) {
    //         return $value;
    //     }
    // }


    /** Register any application services. */
    public function register(): void
    {
        $this->app->bind(LoginViewResponse::class, CustomLoginViewResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     //
    // }

    public function boot(): void
    {
        Gate::define('assignRole', function (User $authUser, User $targetUser) {
            // Admin can assign roles to anyone
            if ($authUser->hasRole('Admin')) {
                return true;
            }

            // Hostel Warden or Mess Manager can assign roles only to their managed users
            if ($authUser->hasRole('Hostel Warden') || $authUser->hasRole('Mess Manager')) {
                return $targetUser->manager_id === $authUser->id;
            }

            return false;
        });

        // --- ProvisionService check for logged-in users ---
        View::composer('*', function ($view) {
            if (Auth::check()) {
                ProvisionService::checkLogin();
            }
        });

        // if (Schema::hasTable('smtp_settings')) {
        //     $smtp = SmtpDetail::first();
        //     if ($smtp) {
        //         Config::set('mail.mailers.smtp.transport', 'smtp');
        //         Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        //         Config::set('mail.mailers.smtp.port', 587);
        //         Config::set('mail.mailers.smtp.encryption', 'tls');
        //         Config::set('mail.mailers.smtp.username', $smtp->username);
        //         Config::set('mail.mailers.smtp.password', $smtp->password);
        //         Config::set('mail.from.address', $smtp->username);
        //         Config::set('mail.from.name', $smtp->from_name);
        //     }
        // }

    }

}

