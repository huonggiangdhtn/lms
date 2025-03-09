<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Exercise\Models\TracNghiemCauhoi;
use App\Modules\Exercise\Models\TracNghiemDapan;
use App\Modules\Exercise\Models\BodeTracNghiem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExerciseController extends Controller
{
    /**
     * Tạo câu hỏi trắc nghiệm
     */
    public function storeQuestion(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'hocphan_id' => 'required|integer|exists:hoc_phans,id',
            'resources' => 'nullable|array',
            'resources.*' => 'integer|exists:resources,id',
            'loai_id' => 'required|integer|exists:trac_nghiem_loais,id',
            'user_id' => 'required|integer|exists:users,id', // Thêm user_id vào validation
        ]);

        try {
            $question = TracNghiemCauhoi::create([
                'content' => $request->content,
                'hocphan_id' => $request->hocphan_id,
                'resources' => $request->resources ? json_encode($request->resources) : null,
                'loai_id' => $request->loai_id,
                'user_id' => $request->user_id, // Lấy từ body request
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo câu hỏi thành công',
                'data' => $question,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo câu hỏi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tạo đáp án cho câu hỏi trắc nghiệm
     */
    public function storeAnswer(Request $request)
    {
        $request->validate([
            'tracnghiem_id' => 'required|integer|exists:trac_nghiem_cauhois,id',
            'content' => 'required|string|max:500',
            'resounce_list' => 'nullable|array',
            'resounce_list.*' => 'integer|exists:resources,id',
            'is_correct' => 'required|boolean',
        ]);

        try {
            $answer = TracNghiemDapan::create([
                'tracnghiem_id' => $request->tracnghiem_id,
                'content' => $request->content,
                'resounce_list' => $request->resounce_list ? json_encode($request->resounce_list) : null,
                'is_correct' => $request->is_correct,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo đáp án thành công',
                'data' => $answer,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo đáp án: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tạo đề thi trắc nghiệm
     */
    public function storeQuiz(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'hocphan_id' => 'required|integer|exists:hoc_phans,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'time' => 'required|integer|min:1',
            'tags' => 'nullable|string|max:255',
            'total_points' => 'required|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.id_question' => 'required|integer|exists:trac_nghiem_cauhois,id',
            'questions.*.points' => 'required|integer|min:1',
            'user_id' => 'required|integer|exists:users,id', // Thêm user_id vào validation
        ]);

        try {
            $calculatedTotal = collect($request->questions)->sum('points');
            if ($calculatedTotal != $request->total_points) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tổng điểm của các câu hỏi không khớp với total_points',
                ], 400);
            }

            $quiz = BodeTracNghiem::create([
                'title' => $request->title,
                'hocphan_id' => $request->hocphan_id,
                'slug' => Str::slug($request->title . '-' . time()),
                'start_time' => Carbon::parse($request->start_time),
                'end_time' => Carbon::parse($request->end_time),
                'time' => $request->time,
                'tags' => $request->tags,
                'user_id' => $request->user_id, // Lấy từ body request
                'total_points' => $request->total_points,
                'questions' => json_encode($request->questions),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo đề thi thành công',
                'data' => $quiz,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo đề thi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy danh sách câu hỏi theo học phần
     */
    public function getQuestionsByHocphan(Request $request)
    {
        $request->validate([
            'hocphan_id' => 'required|integer|exists:hoc_phans,id',
            'user_id' => 'required|integer|exists:users,id', // Thêm user_id vào validation nếu cần lọc
        ]);

        try {
            $questions = TracNghiemCauhoi::where('hocphan_id', $request->hocphan_id)
                ->where('user_id', $request->user_id) // Lọc theo user_id từ request
                ->with(['answers' => function ($query) {
                    $query->select('id', 'tracnghiem_id', 'content', 'is_correct');
                }])
                ->get(['id', 'content', 'hocphan_id', 'loai_id']);

            return response()->json([
                'success' => true,
                'message' => 'Danh sách câu hỏi trắc nghiệm',
                'data' => $questions,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách câu hỏi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
 * Lấy danh sách loại câu hỏi
 */
public function getQuestionTypes(Request $request)
{
    try {
        $types = \App\Modules\Exercise\Models\TracNghiemLoai::all(['id', 'title']);
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách loại câu hỏi',
            'data' => $types,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi khi lấy danh sách loại câu hỏi: ' . $e->getMessage(),
        ], 500);
    }
}
}