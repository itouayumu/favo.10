<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserMainController extends Controller
{
    public function index()
    {
        return view('auth.index');
    }

    public function register()
    {
        return view('auth.register');
    }
}
