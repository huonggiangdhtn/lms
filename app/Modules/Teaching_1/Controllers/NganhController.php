<?php
namespace App\Modules\Teaching_1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Modules\Teaching_1\Models\Nganh;

class NganhController extends Controller
{
    //
    protected $pagesize;
    public function __construct()
    {
        $this->pagesize = env('NUMBER_PER_PAGE', '20');
        $this->middleware('auth');
    }

    public function index()
    {
        $func = "nganh_list";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $active_menu = "nganh_list";
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item active" aria-current="page"> Danh sách Ngành </li>';
        $nganhs = Nganh::orderBy('id', 'DESC')->paginate($this->pagesize);

        return view('Teaching_1::nganh.index', compact('nganhs', 'breadcrumb', 'active_menu'));
    }

    public function create()
    {
        $func = "nganh_add";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $data['donvis'] = \App\Modules\Teaching_1\Models\Donvi::where('status', 'active')->orderBy('title', 'ASC')->get();
        $data['active_menu'] = "nganh_add";
        $data['breadcrumb'] = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item  " aria-current="page"><a href="' . route('admin.nganh.index') . '">Ngành</a></li>
        <li class="breadcrumb-item active" aria-current="page"> Tạo Ngành </li>';
        return view('Teaching_1::nganh.create', $data);
    }

    public function store(Request $request)
    {
        $func = "nganh_add";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $this->validate($request, [
            'title' => 'string|required',
            'donvi_id' => 'numeric|required',
            'code' => 'string|required',
            'content' => 'string|required',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $slug = Str::slug($request->input('title'));
        $slug_count = Nganh::where('slug', $slug)->count();
        if ($slug_count > 0) {
            $slug .= time() . '-' . $slug;
        }
        $data['slug'] = $slug;

        $nganh = Nganh::create($data);
        if ($nganh) {
            return redirect()->route('admin.nganh.index')->with('success', 'Tạo ngành thành công!');
        } else {
            return back()->with('error', 'Có lỗi xảy ra!');
        }
    }

    public function edit(string $id)
    {
        $func = "nganh_edit";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $donvis = \App\Modules\Teaching_1\Models\Donvi::where('status', 'active')->orderBy('title', 'ASC')->get();
        $nganh = Nganh::find($id);
        if ($nganh) {
            $active_menu = "nganh_list";
            $breadcrumb = '
            <li class="breadcrumb-item"><a href="#">/</a></li>
            <li class="breadcrumb-item  " aria-current="page"><a href="' . route('admin.nganh.index') . '">Ngành</a></li>
            <li class="breadcrumb-item active" aria-current="page"> Chỉnh sửa Ngành </li>';
            return view('Teaching_1::nganh.edit', compact('breadcrumb', 'nganh', 'active_menu', 'donvis'));
        } else {
            return back()->with('error', 'Không tìm thấy dữ liệu');
        }
    }

    public function update(Request $request, string $id)
    {
        $func = "nganh_edit";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $nganh = Nganh::find($id);
        if ($nganh) {
            $this->validate($request, [
                'title' => 'string|required',
                'donvi_id' => 'numeric|required',
                'code' => 'string|required',
                'content' => 'string|required',
                'status' => 'required|in:active,inactive',
            ]);
            $data = $request->all();
            $status = $nganh->fill($data)->save();
            if ($status) {
                return redirect()->route('admin.nganh.index')->with('success', 'Cập nhật thành công');
            } else {
                return back()->with('error', 'Có lỗi xảy ra!');
            }
        } else {
            return back()->with('error', 'Không tìm thấy dữ liệu');
        }
    }

    public function destroy(string $id)
    {
        $func = "nganh_delete";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        $nganh = Nganh::find($id);
        if ($nganh) {
            $status = $nganh->delete();
            if ($status) {
                return redirect()->route('admin.nganh.index')->with('success', 'Xóa ngành thành công!');
            } else {
                return back()->with('error', 'Có lỗi xảy ra!');
            }
        } else {
            return back()->with('error', 'Không tìm thấy dữ liệu');
        }
    }

    public function nganhStatus(Request $request)
    {
        $func = "nganh_edit";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        if ($request->mode == 'true') {
            DB::table('nganh')->where('id', $request->id)->update(['status' => 'active']);
        } else {
            DB::table('nganh')->where('id', $request->id)->update(['status' => 'inactive']);
        }
        return response()->json(['msg' => "Cập nhật thành công", 'status' => true]);
    }

    public function nganhSearch(Request $request)
    {
        $func = "nganh_list";
        if (!$this->check_function($func)) {
            return redirect()->route('unauthorized');
        }

        if ($request->datasearch) {
            $active_menu = "nganh_list";
            $searchdata = $request->datasearch;
            $nganhs = DB::table('nganh')->where('title', 'LIKE', '%' . $request->datasearch . '%')
                ->orWhere('content', 'LIKE', '%' . $request->datasearch . '%')
                ->paginate($this->pagesize)->withQueryString();
            $breadcrumb = '
            <li class="breadcrumb-item"><a href="#">/</a></li>
            <li class="breadcrumb-item  " aria-current="page"><a href="' . route('admin.nganh.index') . '">Ngành</a></li>
            <li class="breadcrumb-item active" aria-current="page"> Tìm kiếm </li>';
            return view('Teaching_1::nganh.search', compact('nganhs', 'breadcrumb', 'searchdata', 'active_menu'));
        } else {
            return redirect()->route('admin.nganh.index')->with('success', 'Không có thông tin tìm kiếm!');
        }
    }
}