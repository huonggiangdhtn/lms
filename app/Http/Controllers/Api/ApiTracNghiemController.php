<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Exercise\Models\TracNghiemCauhoi;
use App\Modules\Exercise\Models\TracNghiemDapan;
use App\Modules\Exercise\Models\BoDeTracNghiem;
use App\Models\ExamResult;

class ApiTracNghiemController extends Controller
{
    //
    public function getBoDeTracNghiem()
    {
        $bodetracnghiem = BoDeTracNghiem::all();
        return response()->json($bodetracnghiem);
    }
    public function getTracNghiemDapAn()
    {
        $tracnghiemdapan = TracNghiemDapan::all();
        return response()->json($tracnghiemdapan);
    }
    public function getTracNghiemCauHoi()
    {
        $tracnghiemcauhoi = TracNghiemCauhoi::all();
        return response()->json($tracnghiemcauhoi);
    }

    public function storeKetQuaThi(Request $request)
    {
        $request->validate([
            'results' => 'required|array',
            'results.*.user_id' => 'required|integer',
            'results.*.question_id' => 'required|integer',
            'results.*.selected_answer_id' => 'required|integer',
        ]);
    
        foreach ($request->results as $result) {
            // Kiểm tra xem người dùng đã có kết quả cho câu hỏi này chưa
            $existingResult = ExamResult::where('user_id', $result['user_id'])
                ->where('question_id', $result['question_id'])
                ->first();
    
            if ($existingResult) {
                // Cập nhật kết quả nếu đã tồn tại
                $existingResult->selected_answer_id = $result['selected_answer_id'];
                $existingResult->save();
            } else {
                // Lưu kết quả mới nếu chưa tồn tại
                ExamResult::create($result);
            }
        }
    
        return response()->json(['message' => 'Kết quả đã được lưu thành công!'], 200);
    }

    public function getKetQuaThi(){
        $ketquathi = ExamResult::all();
        return response()->json($ketquathi);
    }
}
