<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function profile_edit()
    {
        return view('users.profile_edit');
    }
}
