<?php

namespace App\Modules\Teo\Controllers;

use App\Http\Controllers\Controller;

class TeoController extends Controller
{
    public function index()
    {
        return view('Teo::index');
    }
}