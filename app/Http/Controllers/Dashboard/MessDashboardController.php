<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class MessDashboardController extends Controller
{
    public function index()
    {
        return view('dashboards.mess');
    }
    
}

