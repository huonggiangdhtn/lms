<?php

namespace App\Modules\Resource\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Resource\Models\ResourceLinkType;
use Illuminate\Http\Request;

class ResourceLinkTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$this->check_function('resource_link_type_access')) { // Thay đổi alias phù hợp
                return redirect()->route('admin.login'); // Hoặc xử lý theo cách bạn muốn
            }
            return $next($request);
        });
    }

    public function index()
    {
        $resourceLinkTypes = ResourceLinkType::all();
        return response()->json($resourceLinkTypes);
    }

    public function create()
    {
        // Có thể thêm logic cho view nếu cần
        return response()->json(['message' => 'Create Resource Link Type']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'code' => 'required|string',
        ]);

        $resourceLinkType = ResourceLinkType::create($request->all());
        return response()->json($resourceLinkType, 201);
    }

    public function show($id)
    {
        $resourceLinkType = ResourceLinkType::findOrFail($id);
        return response()->json($resourceLinkType);
    }

    public function edit($id)
    {
        // Có thể thêm logic cho view nếu cần
        return response()->json(['message' => 'Edit Resource Link Type', 'id' => $id]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string',
            'code' => 'string',
        ]);

        $resourceLinkType = ResourceLinkType::findOrFail($id);
        $resourceLinkType->update($request->all());
        return response()->json($resourceLinkType);
    }

    public function destroy($id)
    {
        $resourceLinkType = ResourceLinkType::findOrFail($id);
        $resourceLinkType->delete();
        return response()->json(null, 204);
    }
}
