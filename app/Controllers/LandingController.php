<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class LandingController extends BaseController
{
    public function index()
    {
        return view('landing');
    }
} 