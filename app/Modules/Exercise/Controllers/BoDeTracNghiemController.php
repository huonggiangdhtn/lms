<?php

namespace App\Modules\Exercise\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Modules\Exercise\Models\Bodetracnghiem; 
use App\Modules\Exercise\Models\HocPhan;
use App\Models\User;
use App\Modules\Exercise\Models\TracNghiemCauhoi;

class BodetracnghiemController extends Controller
{
    protected $pagesize;

    public function __construct()
    {
        $this->pagesize = env('NUMBER_PER_PAGE', '20');
        $this->middleware('auth');
    }

    // List all Bodetracnghiem records
    public function index()
    {
        $active_menu = "bode_tracnghiem_list";
        $breadcrumb = '<li class="breadcrumb-item"><a href="#">/</a></li>
                       <li class="breadcrumb-item active" aria-current="page">Danh sách bộ đề trắc nghiệm</li>';

        $bodetracnghiem = Bodetracnghiem::orderBy('id', 'DESC')->paginate($this->pagesize);
        // Tính số lượng câu hỏi từ JSON
        $bodetracnghiem->getCollection()->transform(function ($item) {
        $questions = $item->questions;
        $item->so_cau_hoi = is_array($questions) ? count($questions) : 0; // Đếm số lượng phần tử
        return $item;
    });
        $hocPhanList = HocPhan::pluck('title', 'id')->toArray();
        $userList = User::pluck('full_name', 'id')->toArray();

        return view('Exercise::bode_tracnghiem.index', compact('bodetracnghiem', 'breadcrumb', 'active_menu', 'hocPhanList', 'userList'));
    }

    // Show the form for creating a new Bodetracnghiem
    public function create()
    {
        $active_menu = 'bode_tracnghiem_add';
        $cauHois = TracNghiemCauHoi::all(); // Lấy tất cả câu hỏi
        $hocphan = HocPhan::all();
        $users = User::all();
        $tags = \App\Models\Tag::where('status','active')->orderBy('title','ASC')->get();
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item active" aria-current="page">Thêm câu hỏi trắc nghiệm</li>';

    return view('Exercise::bode_tracnghiem.create', compact('cauHois','hocphan', 'users','tags','breadcrumb','active_menu'));
    }

    // Store a new Bodetracnghiem record
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'hocphan_id' => 'required|exists:hoc_phans,id',
            'slug' => 'nullable|string|max:255|unique:bode_tracnghiem,slug',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'time' => 'required|integer|min:1',
            'tags' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'total_points' => 'required|integer|min:0',
            'selected_questions' => 'nullable|array', // Mảng các câu hỏi được chọn
            'points' => 'nullable|array', // Mảng điểm cho các câu hỏi
        ]);
    
        // Xử lý slug tự động nếu không nhập
        if (empty($validatedData['slug'])) {
            $validatedData['slug'] = Str::slug($validatedData['title']);
        }
    
        // Tạo danh sách câu hỏi dưới dạng JSON
        $questions = [];
        $selectedQuestions = $request->input('selected_questions', []);
        $points = $request->input('points', []);
    
        foreach ($selectedQuestions as $questionId) {
            $questions[] = [
                'id_question' => $questionId,
                'points' => $points[$questionId] ?? 0,
            ];
        }
        $validatedData['questions'] = json_encode($questions);
    
        // Tạo bộ đề trắc nghiệm
        $bodetracnghiem = Bodetracnghiem::create($validatedData);
    
        // Liên kết tag nếu có
        $tag_ids = $request->tag_ids;
        if (!empty($tag_ids)) {
            $tagservice = new \App\Http\Controllers\TagController();
            $tagservice->store_Bodetracnghiem_tag($bodetracnghiem->id, $tag_ids);
        }
    
        // Chuyển hướng với thông báo thành công
        return redirect()->route('admin.bode_tracnghiem.index')->with('success', 'Bộ đề trắc nghiệm được tạo thành công.');
    }
    function createMultipleBode($hocphan_id, $total_points, $number_of_bodes = 10)
    {
        $bodes = []; // Mảng lưu danh sách bộ đề đã tạo
    
        for ($i = 1; $i <= $number_of_bodes; $i++) {
            // Tạo thông tin bộ đề
            $title = "Bộ đề tự động #" . $i;
            $slug = "bo-de-tu-dong-" . $i . "-" . time();
            $start_time = now()->addDays($i); // Thời gian bắt đầu, mỗi bộ đề cách nhau 1 ngày
            $end_time = $start_time->copy()->addHours(2); // Thời gian kết thúc
            $time = 120; // Thời gian làm bài (phút)
            $tags = "Tự động, Bộ đề #" . $i;
            $user_id = 1; // ID người tạo (có thể thay đổi)
    
            // Lấy danh sách loai_id có câu hỏi thuộc hocphan_id
            $loaiIds = TracNghiemCauhoi::where('hocphan_id', $hocphan_id)
                ->distinct()
                ->pluck('loai_id')
                ->toArray();
    
            $questions = [];
    
            // Duyệt qua từng loai_id và lấy 10 câu hỏi ngẫu nhiên
            foreach ($loaiIds as $loaiId) {
                $randomQuestions = TracNghiemCauhoi::where('hocphan_id', $hocphan_id)
                    ->where('loai_id', $loaiId)
                    ->inRandomOrder()
                    ->take(10)
                    ->pluck('id')
                    ->toArray();
    
                $questions = array_merge($questions, $randomQuestions);
            }
    
            // Tính điểm cho từng câu hỏi
            $numQuestions = count($questions);
            $questionsWithPoints = [];
    
            if ($numQuestions > 0) {
                $pointsPerQuestion = $total_points / $numQuestions; // Điểm cho mỗi câu hỏi
    
                foreach ($questions as $id_question) {
                    $questionsWithPoints[] = [
                        'id_question' => $id_question,
                        'points' => round($pointsPerQuestion, 2), // Làm tròn đến 2 chữ số
                    ];
                }
            }
    
            // Tạo bộ đề
            $bode = TracNghiemBode::create([
                'title' => $title,
                'hocphan_id' => $hocphan_id,
                'slug' => $slug,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'time' => $time,
                'tags' => $tags,
                'user_id' => $user_id,
                'total_points' => $total_points,
                'questions' => $questionsWithPoints, // Lưu danh sách câu hỏi và điểm tương ứng
            ]);
    
            $bodes[] = $bode; // Thêm vào danh sách bộ đề
        }
    
        return $bodes; // Trả về danh sách các bộ đề đã tạo
    }
    
    function createAutoBodeWithPoints($title, $hocphan_id, $slug, $start_time, $end_time, $time, $tags, $user_id, $total_points)
    {
        // Lấy danh sách loai_id có câu hỏi thuộc hocphan_id
        $loaiIds = TracNghiemCauhoi::where('hocphan_id', $hocphan_id)
            ->distinct()
            ->pluck('loai_id')
            ->toArray();

        $questions = [];

        // Lấy 10 câu hỏi ngẫu nhiên cho mỗi loai_id
        foreach ($loaiIds as $loaiId) {
            $randomQuestions = TracNghiemCauhoi::where('hocphan_id', $hocphan_id)
                ->where('loai_id', $loaiId)
                ->inRandomOrder()
                ->take(10)
                ->pluck('id')
                ->toArray();

            $questions = array_merge($questions, $randomQuestions);
        }

        // Tạo bộ đề mới
        $bode = TracNghiemBode::create([
            'title' => $title,
            'hocphan_id' => $hocphan_id,
            'slug' => $slug,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'time' => $time,
            'tags' => $tags,
            'user_id' => $user_id,
            'total_points' => $total_points,
            'questions' => $questions, // Tạm lưu danh sách ID câu hỏi
        ]);

        // Phân phối điểm đều cho từng câu hỏi
        $bode->distributePoints();

        return $bode;
    }


    // Show a specific Bodetracnghiem record
    public function show(Bodetracnghiem $bode_tracnghiem)
    {
        $active_menu = 'bode_tracnghiem_show';

        $questions = $bode_tracnghiem->tracnghiemCauhois();

        return view('Exercise::bode_tracnghiem.show', compact('bode_tracnghiem', 'active_menu', 'questions'));
    }

    // Hiển thị form chỉnh sửa bộ đề trắc nghiệm
    public function edit($id)
    {
        $active_menu = 'bode_tracnghiem_edit';
        $bodetracnghiem = Bodetracnghiem::findOrFail($id);
        $cauHois = TracNghiemCauHoi::all(); // Lấy tất cả câu hỏi
        $hocphan = HocPhan::all();
        $users = User::all();
        $tags = \App\Models\Tag::where('status', 'active')->orderBy('title', 'ASC')->get();
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa bộ đề trắc nghiệm</li>';
    
        // Decode questions từ JSON để hiển thị trong form chỉnh sửa
        $selectedQuestions = json_decode($bodetracnghiem->questions, true) ?? [];
    
        return view('Exercise::bode_tracnghiem.edit', compact(
            'bodetracnghiem',
            'cauHois',
            'hocphan',
            'users',
            'tags',
            'breadcrumb',
            'active_menu',
            'selectedQuestions'
        ));
    }
    
    public function update(Request $request, $id)
    {
        try {
            $bodetracnghiem = Bodetracnghiem::findOrFail($id);
    
            // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'hocphan_id' => 'required|exists:hoc_phans,id',
                'slug' => 'nullable|string|max:255|unique:bode_tracnghiem,slug,' . $id,
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'time' => 'required|integer|min:1',
                'tags' => 'nullable|string|max:255',
                'user_id' => 'required|exists:users,id',
                'total_points' => 'required|integer|min:0',
                'selected_questions' => 'nullable|array', // Mảng các câu hỏi được chọn
                'points' => 'nullable|array', // Mảng điểm cho các câu hỏi
            ]);
    
            // Xử lý slug tự động nếu không nhập
            if (empty($validatedData['slug'])) {
                $validatedData['slug'] = Str::slug($validatedData['title']);
            }
    
            // Xử lý danh sách câu hỏi
            $questions = [];
            $selectedQuestions = $request->input('selected_questions', []);
            $points = $request->input('points', []);
    
            foreach ($selectedQuestions as $questionId) {
                $questions[] = [
                    'id_question' => $questionId,
                    'points' => $points[$questionId] ?? 0,
                ];
            }
    
            $validatedData['questions'] = json_encode($questions);
    
            // Cập nhật dữ liệu vào DB
            $bodetracnghiem->update($validatedData);
    
            // Xử lý liên kết tag
            $tag_ids = $request->tag_ids;
            if (!empty($tag_ids)) {
                $tagservice = new \App\Http\Controllers\TagController();
                $tagservice->store_Bodetracnghiem_tag($bodetracnghiem->id, $tag_ids);
            }
    
            // Redirect với thông báo thành công
            return redirect()->route('admin.bode_tracnghiem.index')->with('success', 'Bộ đề trắc nghiệm được cập nhật thành công.');
        } catch (\Exception $e) {
            // Log lỗi chi tiết
            Log::error('Error updating Bodetracnghiem:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            // Redirect với thông báo lỗi
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật dữ liệu.');
        }
    }
    


    // Delete a Bodetracnghiem record
    public function destroy($id)
    {
        $bodetracnghiem = Bodetracnghiem::findOrFail($id);
        $bodetracnghiem->delete();

        return redirect()->route('admin.bode_tracnghiem.index')->with('success', 'Bộ đề trắc nghiệm đã được xóa thành công.');
    }
}
