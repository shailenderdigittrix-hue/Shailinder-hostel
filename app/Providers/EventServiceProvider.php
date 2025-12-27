<?php

namespace App\Providers;

use App\Services\ProvisionService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;


class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();
        Event::listen(Login::class, function ($event) {
            ProvisionService::checkLogin();
        });

    }
}
