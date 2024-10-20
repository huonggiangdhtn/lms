<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Support\Facades\Auth;

class BlogCategoryController extends Controller
{
    protected $pagesize;

    public function __construct()
    {
        $this->pagesize = env('NUMBER_PER_PAGE', '20');
    }

    public function index()
    {
        $func = "bcat_list";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $active_menu = "blogcat_list";
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item active" aria-current="page">Danh mục bài viết</li>';

        $blogcats = BlogCategory::orderBy('id', 'DESC')->paginate($this->pagesize);
        return view('Blog::blogcategory.index', compact('blogcats', 'breadcrumb', 'active_menu'));
    }

    public function create()
    {
        $func = "bcat_add";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $active_menu = "blogcat_add";
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="' . route('admin.blogcategory.index') . '">Danh mục bài viết</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tạo danh mục bài viết</li>';

        return view('Blog::blogcategory.create', compact('breadcrumb', 'active_menu'));
    }

    public function store(Request $request)
    {
        $func = "bcat_add";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $this->validate($request, [
            'title' => 'string|required',
            'photo' => 'image|nullable|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $helpController = new \App\Http\Controllers\HelpController();
        $data['content'] = $helpController->uploadImageInContent($data['content']);
        $data['content'] = $helpController->removeImageStyle($data['content']);

        $slug = Str::slug($request->input('title'));
        if (BlogCategory::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }
        $data['slug'] = $slug;

        // Xử lý tải ảnh
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images', $filename, 'public');
            $data['photo'] = 'storage/' . $path;
        } else {
            $data['photo'] = asset('backend/images/profile-6.jpg'); // Ảnh mặc định nếu không tải lên
        }

        // Lấy ID người dùng hiện tại
        $user = Auth::user();
        if ($user) {
            $data['user_id'] = $user->id;
        } else {
            return back()->with('error', 'Bạn cần đăng nhập để tạo danh mục bài viết.');
        }

        $status = BlogCategory::create($data);
        return $status
            ? redirect()->route('admin.blogcategory.index')->with('success', 'Tạo danh mục bài viết thành công!')
            : back()->with('error', 'Có lỗi xảy ra!');
    }

    public function edit(string $id)
    {
        $func = "bcat_edit";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $blogcat = BlogCategory::find($id);
        if (!$blogcat) {
            return back()->with('error', 'Không tìm thấy dữ liệu');
        }

        $active_menu = "blogcat_list";
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="' . route('admin.blogcategory.index') . '">Danh mục bài viết</a></li>
        <li class="breadcrumb-item active" aria-current="page">Điều chỉnh mục bài viết</li>';

        return view('Blog::blogcategory.edit', compact('breadcrumb', 'blogcat', 'active_menu'));
    }

    public function update(Request $request, string $id)
    {
        $func = "bcat_edit";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $blogcat = BlogCategory::find($id);
        if (!$blogcat) {
            return back()->with('error', 'Không tìm thấy dữ liệu');
        }

        $this->validate($request, [
            'title' => 'string|required',
            'photo' => 'image|nullable|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();

        // Xử lý tải ảnh
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images', $filename, 'public');
            $data['photo'] = 'storage/' . $path;
        } else {
            $data['photo'] = $blogcat->photo ?? asset('backend/images/profile-6.jpg');
        }

        $status = $blogcat->fill($data)->save();
        return $status
            ? redirect()->route('admin.blogcategory.index')->with('success', 'Cập nhật thành công')
            : back()->with('error', 'Có lỗi xảy ra!');
    }

    public function destroy(string $id)
    {
        $func = "bcat_delete";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $blogcat = BlogCategory::find($id);
        if ($blogcat) {
            return $blogcat->delete()
                ? redirect()->route('admin.blogcategory.index')->with('success', 'Xóa danh mục thành công!')
                : back()->with('error', 'Có lỗi xảy ra!');
        } else {
            return back()->with('error', 'Không tìm thấy dữ liệu');
        }
    }

    public function blogcatStatus(Request $request)
    {
        $status = $request->mode == 'true' ? 'active' : 'inactive';
        DB::table('blog_categories')->where('id', $request->id)->update(['status' => $status]);
        return response()->json(['msg' => "Cập nhật thành công", 'status' => true]);
    }

    public function blogcatSearch(Request $request)
    {
        $func = "bcat_list";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $active_menu = "blogcat_list";
        $searchdata = $request->input('datasearch');

        // Kiểm tra xem có dữ liệu tìm kiếm không
        if ($searchdata) {
            $blogcats = BlogCategory::where('title', 'LIKE', '%' . $searchdata . '%')
                ->orderBy('id', 'DESC')
                ->paginate($this->pagesize)
                ->withQueryString();

            $breadcrumb = '
            <li class="breadcrumb-item"><a href="#">/</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="' . route('admin.blog.index') . '">Bài viết</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>';

            // Thông báo tìm kiếm
            $message = $blogcats->isEmpty()
                ? 'Không tìm thấy danh mục nào với từ khóa: ' . $searchdata
                : 'Tìm thấy ' . $blogcats->total() . ' danh mục với từ khóa: ' . $searchdata;

            return view('Blog::blogcategory.index', compact('blogcats', 'breadcrumb', 'active_menu', 'searchdata', 'message'));
        } else {
            return redirect()->route('admin.blogcategory.index')->with('error', 'Bạn cần nhập thông tin tìm kiếm.');
        }
    }
}
