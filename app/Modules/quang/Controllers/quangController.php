<?php

namespace App\Modules\quang\Controllers;

use App\Http\Controllers\Controller;

class quangController extends Controller
{
    public function index()
    {
        return view('quang::index');
    }
}