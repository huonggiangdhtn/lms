<?php

namespace App\Modules\Huong\Controllers;

use App\Http\Controllers\Controller;

class HuongController extends Controller
{
    public function index()
    {
        return view('Huong::index');
    }
}