<?php

namespace App\Modules\Teaching_1\Controllers;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Modules\Teaching_1\Models\Donvi;

class DonviController extends Controller
{
    public function index()
    {
        // Sử dụng Model để lấy dữ liệu
        $donVis = Donvi::all();
        return response()->json($donVis);
        // hoặc
        // return view('donvi.index', compact('donvis'));
    }
}
