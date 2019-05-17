<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function index()
    {
        $test = Hash::make('liangzelee@gmail.com');
        dd($test);
    }
}
