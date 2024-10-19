<?php

namespace App\Modules\long\Controllers;

use App\Http\Controllers\Controller;

class longController extends Controller
{
    public function index()
    {
        return view('long::index');
    }
}