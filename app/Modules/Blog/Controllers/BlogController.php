<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Modules\Blog\Models\Blog;
use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    protected $pagesize;

    public function __construct()
    {
        $this->pagesize = env('NUMBER_PER_PAGE', '20');
        $this->middleware('auth'); // Đảm bảo người dùng đã đăng nhập
    }

    public function index()
    {
        $func = "blog_list";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $active_menu = "blog_list";
        $breadcrumb = '
            <li class="breadcrumb-item"><a href="#">/</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh sách bài viết</li>';
        $blogs = Blog::orderBy('id', 'DESC')->paginate($this->pagesize);

        return view('Blog::blog.index', compact('blogs', 'breadcrumb', 'active_menu'));
    }

    public function create()
    {
        $func = "blog_add";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $data['categories'] = BlogCategory::where('status', 'active')->orderBy('title', 'ASC')->get();
        $data['tags'] = \App\Models\Tag::where('status', 'active')->orderBy('title', 'ASC')->get();
        $data['active_menu'] = "blog_add";
        $data['breadcrumb'] = '
            <li class="breadcrumb-item"><a href="#">/</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="' . route('admin.blog.index') . '">Bài viết</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tạo bài viết</li>';

        return view('Blog::blog.create', $data);
    }

    public function store(Request $request)
    {
        $func = "blog_add";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $this->validate($request, [
            'title' => 'string|required',
            'photo' => 'string|nullable',
            'summary' => 'string|required',
            'content' => 'string|required',
            'cat_id' => 'numeric|nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $helpController = new \App\Http\Controllers\HelpController();
        $data['content'] = $helpController->uploadImageInContent($data['content']);
        $data['content'] = $helpController->removeImageStyle($data['content']);

        $slug = Str::slug($request->input('title'));
        if (Blog::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }
        $data['slug'] = $slug;

        $data['photo'] = $request->photo ?? asset('backend/images/profile-6.jpg');

        // Lấy ID người dùng hiện tại
        $user = Auth::user(); // Sử dụng Auth facade
        if ($user) {
            $data['user_id'] = $user->id;
        } else {
            return back()->with('error', 'Bạn cần đăng nhập để tạo bài viết.');
        }

        $blog = Blog::create($data);
        if ($blog) {
            $tagservice = new \App\Http\Controllers\TagController();
            $tagservice->store_blog_tag($blog->id, $request->tag_ids);
            return redirect()->route('admin.blog.index')->with('success', 'Tạo bài viết thành công!');
        } else {
            return back()->with('error', 'Có lỗi xảy ra!');
        }
    }

    public function edit(string $id)
    {
        $func = "blog_edit";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $blog = Blog::find($id);
        if (!$blog) {
            return back()->with('error', 'Không tìm thấy dữ liệu');
        }

        $categories = BlogCategory::where('status', 'active')->orderBy('title', 'ASC')->get();
        $tags = \App\Models\Tag::where('status', 'active')->orderBy('title', 'ASC')->get();
        $tag_ids = DB::table('tag_blogs')->where('blog_id', $blog->id)->pluck('tag_id');

        $active_menu = "blog_list";
        $breadcrumb = '
            <li class="breadcrumb-item"><a href="#">/</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="' . route('admin.blog.index') . '">Bài viết</a></li>
            <li class="breadcrumb-item active" aria-current="page">Điều chỉnh bài viết</li>';

        return view('Blog::blog.edit', compact('breadcrumb', 'blog', 'active_menu', 'categories', 'tag_ids', 'tags'));
    }

    public function update(Request $request, string $id)
    {
        $func = "blog_edit";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $blog = Blog::find($id);
        if (!$blog) {
            return back()->with('error', 'Không tìm thấy dữ liệu');
        }

        $this->validate($request, [
            'title' => 'string|required',
            'photo' => 'string|nullable',
            'summary' => 'string|required',
            'content' => 'string|required',
            'cat_id' => 'numeric|nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $helpController = new \App\Http\Controllers\HelpController();
        $data['content'] = $helpController->uploadImageInContent($data['content']);

        if (empty($data['photo'])) {
            $data['photo'] = $blog->photo ?? asset('backend/images/profile-6.jpg');
        }

        $status = $blog->fill($data)->save();
        if ($status) {
            $tagservice = new \App\Http\Controllers\TagController();
            $tagservice->update_blog_tag($blog->id, $request->tag_ids);
            return redirect()->route('admin.blog.index')->with('success', 'Cập nhật thành công');
        } else {
            return back()->with('error', 'Có lỗi xảy ra!');
        }
    }

    public function destroy(string $id)
    {
        $func = "blog_delete";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $blog = Blog::find($id);
        if ($blog) {
            $status = $blog->delete();
            return $status
                ? redirect()->route('admin.blog.index')->with('success', 'Xóa danh mục thành công!')
                : back()->with('error', 'Có lỗi xảy ra!');
        } else {
            return back()->with('error', 'Không tìm thấy dữ liệu');
        }
    }

    public function blogStatus(Request $request)
    {
        $func = "blog_edit";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $status = $request->mode == 'true' ? 'active' : 'inactive';
        DB::table('blogs')->where('id', $request->id)->update(['status' => $status]);

        return response()->json(['msg' => "Cập nhật thành công", 'status' => true]);
    }

    public function blogSearch(Request $request)
    {
        $func = "blog_list";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $active_menu = "blog_list";
        $searchdata = $request->datasearch;

        if ($searchdata) {
            $blogs = Blog::where('title', 'LIKE', '%' . $searchdata . '%')
                ->orWhere('content', 'LIKE', '%' . $searchdata . '%')
                ->paginate($this->pagesize)
                ->withQueryString();

            $breadcrumb = '
            <li class="breadcrumb-item"><a href="#">/</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="' . route('admin.blog.index') . '">Bài viết</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>';

            // Nếu không tìm thấy bài viết nào
            if ($blogs->isEmpty()) {
                return view('Blog::blog.search', compact('blogs', 'breadcrumb', 'searchdata', 'active_menu'))->with('error', 'Không tìm thấy bài viết nào với từ khóa: ' . $searchdata);
            }

            return view('Blog::blog.search', compact('blogs', 'breadcrumb', 'searchdata', 'active_menu'));
        } else {
            return redirect()->route('admin.blog.index')->with('error', 'Bạn cần nhập thông tin tìm kiếm.');
        }
    }
}
