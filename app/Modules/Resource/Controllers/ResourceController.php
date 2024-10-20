<?php

namespace App\Modules\Resource\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Resource\Models\Resource;
use App\Modules\Resource\Models\ResourceLinkType;
use App\Modules\Resource\Models\ResourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    protected $pagesize;

    public function __construct()
    {
        $this->pagesize = env('NUMBER_PER_PAGE', '20');
        $this->middleware('auth');
    }

    public function index()
    {
        $resources = Resource::orderBy('id', 'DESC')->paginate($this->pagesize);
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item active" aria-current="page">Danh sách tài nguyên</li>';
        $active_menu = "resource_list";

        return view('Resource::index', compact('resources', 'breadcrumb', 'active_menu'));
    }

    public function create()
    {
        $resourceTypes = ResourceType::all();
        $linkTypes = ResourceLinkType::all();
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tạo tài nguyên</li>';
        $active_menu = "resource_add";

        return view('Resource::create', compact('resourceTypes', 'linkTypes', 'breadcrumb', 'active_menu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'resource_type_id' => 'required|exists:resource_types,id',
            'resource_link_type_id' => 'required|exists:resource_link_types,id',
            'file' => 'required|file|mimes:jpg,jpeg,png,mp4,mp3,pdf,doc,mov,docx,ppt,pptx,xls,xlsx|max:2048',
            'tags' => 'nullable|string',
        ]);

        $slug = Str::slug($request->title);
        $count = Resource::where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Sử dụng disk 'public' để lưu trữ file
        $filePath = $file->storeAs('uploads/resources', $fileName, 'public');

        Resource::create([
            'title' => $request->title,
            'slug' => $slug,
            'file_name' => $fileName,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'path' => $filePath,
            'url' => '/storage/' . $filePath,  // Đảm bảo URL bắt đầu với /storage/
            'resource_type_id' => $request->resource_type_id,
            'resource_link_type_id' => $request->resource_link_type_id,
            'tags' => $request->tags,
        ]);

        return redirect()->route('admin.resources.index')->with('success', 'Tạo tài nguyên thành công.');
    }

    public function edit($id)
    {
        $resource = Resource::findOrFail($id);
        $resourceTypes = ResourceType::all();
        $linkTypes = ResourceLinkType::all();
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa tài nguyên</li>';
        $active_menu = "resource_edit";

        return view('Resource::edit', compact('resource', 'resourceTypes', 'linkTypes', 'breadcrumb', 'active_menu'));
    }

    public function update(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'resource_type_id' => 'required|exists:resource_types,id',
            'resource_link_type_id' => 'required|exists:resource_link_types,id',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mp3,pdf,doc,mov,docx,ppt,pptx,xls,xlsx|max:20480',
            'tags' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/resources', $fileName, 'public');

            // Xóa tệp cũ
            if (Storage::disk('public')->exists(str_replace('storage/', '', $resource->path))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $resource->path));
            }

            $resource->file_name = $fileName;
            $resource->file_type = $file->getClientMimeType();
            $resource->file_size = $file->getSize();
            $resource->path = 'storage/' . $filePath;  // Thêm "storage/" vào path
            $resource->url = Storage::url($filePath);  // Cập nhật URL
        }

        $resource->title = $request->title;
        $resource->resource_type_id = $request->resource_type_id;
        $resource->resource_link_type_id = $request->resource_link_type_id;
        $resource->tags = $request->tags;
        $resource->save();

        return redirect()->route('admin.resources.index')->with('success', 'Cập nhật tài nguyên thành công.');
    }

    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);

        if (Storage::disk('public')->exists(str_replace('storage/', '', $resource->path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $resource->path));
        }

        $resource->delete();

        return redirect()->route('admin.resources.index')->with('success', 'Xóa tài nguyên thành công.');
    }

    public function show($slug)
    {
        $resource = Resource::where('slug', $slug)->firstOrFail();
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="#">/</a></li>
        <li class="breadcrumb-item active" aria-current="page">Chi tiết tài nguyên</li>';
        $active_menu = "resource_detail";

        return view('Resource::show', compact('resource', 'breadcrumb', 'active_menu'));
    }
    
}
