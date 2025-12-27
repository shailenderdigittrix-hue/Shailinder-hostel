<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Hostel Warden')) {
            return redirect()->route('warden.dashboard');
        }

        if ($user->hasRole('Mess Manager')) {
            return redirect()->route('mess.dashboard');
        }

        // Default redirect for any other roles or users
        return redirect(HOME);
    }
}

