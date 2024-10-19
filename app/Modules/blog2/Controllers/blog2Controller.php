<?php

namespace App\Modules\blog2\Controllers;

use App\Http\Controllers\Controller;

class blog2Controller extends Controller
{
    public function index()
    {
        return view('blog2::index');
    }
}