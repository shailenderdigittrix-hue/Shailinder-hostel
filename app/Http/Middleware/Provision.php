<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\ProvisionService;

class Provision
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            ProvisionService::checkLogin();
        }

        return $next($request);
    }
}

