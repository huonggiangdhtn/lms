<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Modules\Teaching_2\Models\HocPhan;
use App\Modules\Exercise\Models\TracNghiemCauhoi;
use App\Modules\Resource\Models\Resource;

class VideoHocController extends Controller
{
    public function getVideo() {
        $tracnghiem = TracNghiemCauhoi::all(); // Lấy tất cả dữ liệu từ bảng
    
        foreach ($tracnghiem as $data) {
            $resources = json_decode($data->resources, true); // Giải mã JSON thành mảng
            if ($resources) { // Kiểm tra nếu JSON được giải mã thành công
                return response()->json($resources); // Trả về từng giá trị resources
            }
        }
    
        // Trường hợp không có dữ liệu
        return response()->json(['message' => 'No resources found'], 404);
    }
    
}