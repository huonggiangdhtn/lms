<?php

namespace App\Modules\Teaching_1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Modules\Teaching_1\Models\Student;

class StudentController extends Controller
{
    protected $pagesize;

    public function __construct()
    {
        $this->pagesize = env('NUMBER_PER_PAGE', 20);
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorizeFunction("student_list");

        $active_menu = "student_list";
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item active" aria-current="page">Danh sách Sinh viên</li>';

        $students = Student::with(['donvi', 'nganh'])->orderBy('id', 'DESC')->paginate($this->pagesize);

        return view('Teaching_1::student.index', compact('students', 'breadcrumb', 'active_menu'));
    }

    public function create()
    {
        $this->authorizeFunction("student_add");

        $data['donvis'] = \App\Modules\Teaching_1\Models\Donvi::orderBy('title', 'ASC')->get();
        $data['nganhs'] = \App\Modules\Teaching_1\Models\Nganh::orderBy('title', 'ASC')->get();
        $data['active_menu'] = "student_add";
        $data['breadcrumb'] = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="' . route('student.index') . '">Sinh viên</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tạo Sinh viên</li>';

        return view('Teaching_1::student.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorizeFunction("student_add");

        // Validate dữ liệu
        $this->validateRequest($request);

        // Lấy dữ liệu từ form
        $data = $request->all();

        // Tạo user mới
        $user = \App\Models\User::create([
            'full_name' => $request->input('name'),
            'email' => $request->input('mssv') . '@gmail.com',
            'password' => $request->input('mssv'), 
            'role' => 'sinhvien',
            'status' => 'active',
            'phone' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Cập nhật user_id cho student
        $data['user_id'] = $user->id;

        // Tạo slug từ mssv
        $data['slug'] = $this->generateUniqueSlug($request->input('mssv') . '-' . now()->timestamp);

        // Tạo mới sinh viên
        $student = Student::create($data);

        return $student
            ? redirect()->route('student.index')->with('success', 'Tạo sinh viên thành công!')
            : back()->with('error', 'Có lỗi xảy ra!');
    }

    public function edit(string $id)
    {
        $this->authorizeFunction("student_edit");

        $donvis = \App\Modules\Teaching_1\Models\Donvi::orderBy('title', 'ASC')->get();
        $nganhs = \App\Modules\Teaching_1\Models\Nganh::orderBy('title', 'ASC')->get();
        $student = Student::findOrFail($id);

        $active_menu = "student_list";
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="' . route('student.index') . '">Sinh viên</a></li>
        <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa Sinh viên</li>';

        return view('Teaching_1::student.edit', compact('breadcrumb', 'student', 'active_menu', 'donvis', 'nganhs'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeFunction("student_edit");

        // Validate dữ liệu
        $this->validateRequest($request, $id);

        // Lấy dữ liệu từ form
        $data = $request->all();

        // Tìm sinh viên và người dùng liên quan
        $student = Student::findOrFail($id);
        $user = $student->user;

        // Cập nhật slug từ mssv
        $data['slug'] = $this->generateUniqueSlug($request->input('mssv') . '-' . now()->timestamp, $id);

        // Cập nhật thông tin sinh viên
        $student->update($data);

        // Cập nhật thông tin user liên quan
        if ($user) {
            $user->update([
                'full_name' => $request->input('name'),
                'email' => $request->input('mssv') . '@domain.com',
            ]);
        }

        return redirect()->route('student.index')->with('success', 'Cập nhật sinh viên thành công!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $user = $student->user;

        $student->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route('student.index')->with('success', 'Sinh viên đã được xóa!');
    }

    public function studentStatus(Request $request)
    {
        $func = "student_edit";
        if (!$this->check_function($func)) {
            return response()->json(['msg' => "Bạn không có quyền cập nhật trạng thái sinh viên", 'status' => false]);
        }

        $status = $request->mode == 'true' ? 'đang học' : 'thôi học';
        DB::table('students')->where('id', $request->id)->update(['status' => $status]);

        return response()->json(['msg' => "Cập nhật trạng thái sinh viên thành công", 'status' => true]);
    }

    protected function validateRequest(Request $request, $studentId = null)
{
    $request->validate([
        'name' => 'string|required|max:255',
        'mssv' => 'string|required|unique:students,mssv,' . $studentId,
        'donvi_id' => 'numeric|required',
        'nganh_id' => 'numeric|required',
        'khoa' => 'string|required',
        'status' => 'required|in:đang học,thôi học,tốt nghiệp',
        
    ]);
}

    protected function generateUniqueSlug($slug, $existingId = null)
    {
        $slugCount = Student::where('slug', $slug)
            ->when($existingId, function ($query) use ($existingId) {
                return $query->where('id', '!=', $existingId);
            })
            ->count();

        return $slugCount > 0 ? $slug . '-' . uniqid() : $slug;
    }

    protected function authorizeFunction($func)
    {
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }
    }
}
