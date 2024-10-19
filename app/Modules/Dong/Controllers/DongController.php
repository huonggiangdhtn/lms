<?php

namespace App\Modules\Dong\Controllers;

use App\Http\Controllers\Controller;

class DongController extends Controller
{
    public function index()
    {
        return view('Dong::index');
    }
}