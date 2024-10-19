<?php

namespace App\Modules\son\Controllers;

use App\Http\Controllers\Controller;

class sonController extends Controller
{
    public function index()
    {
        return view('son::index');
    }
}