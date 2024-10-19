<?php

namespace App\Modules\thu\Controllers;

use App\Http\Controllers\Controller;

class thuController extends Controller
{
    public function index()
    {
        return view('thu::index');
    }
}