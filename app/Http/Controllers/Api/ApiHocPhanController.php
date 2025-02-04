<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Teaching_2\Models\HocPhan;
use App\Modules\Exercise\Models\Tuluancauhoi;
use App\Modules\Resource\Models\Resource;

class ApiHocPhanController extends Controller
{
    //
    public function getHocPhan(){
        $hocPhans = HocPhan::all();
        return response()->json($hocPhans); 
    }
}
