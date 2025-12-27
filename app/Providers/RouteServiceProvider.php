<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after login.
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        // logger('RouteServiceProvider booted');
        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // Main web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Admin routes
            Route::middleware(['web', 'auth', 'role:Admin'])
                ->prefix('admin') // URL: /admin/...
                ->name('admin.')  // Route name: admin.dashboard
                ->group(base_path('routes/admin.php'));

            // Warden routes
            Route::middleware(['web', 'auth', 'role:Hostel Warden'])
                ->prefix('warden')
                ->name('warden.')
                ->group(base_path('routes/warden.php'));

            // Mess Manager routes
            Route::middleware(['web', 'auth', 'role:Mess Manager'])
                ->prefix('mess')
                ->name('mess.')
                ->group(base_path('routes/mess.php'));
        });
        
    }

    
}
