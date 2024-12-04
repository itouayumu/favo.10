<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecommendController extends Controller
{
    public function index()
    {
        return view('recommends.recommendList');
    }
}
