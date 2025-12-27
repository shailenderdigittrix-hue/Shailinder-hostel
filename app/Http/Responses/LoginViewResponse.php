<?php

namespace App\Http\Responses; // Important!

use Laravel\Fortify\Contracts\LoginViewResponse as LoginViewResponseContract;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class LoginViewResponse implements LoginViewResponseContract
{
    public function toResponse($request)
    {
        return view('auth.login'); 
    }
}
