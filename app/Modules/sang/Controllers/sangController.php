<?php

namespace App\Modules\sang\Controllers;

use App\Http\Controllers\Controller;

class sangController extends Controller
{
    public function index()
    {
        return view('sang::index');
    }
}