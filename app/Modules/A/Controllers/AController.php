<?php

namespace App\Modules\A\Controllers;

use App\Http\Controllers\Controller;

class AController extends Controller
{
    public function index()
    {
        return view('A::index');
    }
}