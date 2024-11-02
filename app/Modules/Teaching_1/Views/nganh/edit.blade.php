@extends('backend.layouts.master')
@section('content')

<div class="intro-y flex items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Chỉnh sửa ngành</h2>
</div>
<div class="grid grid-cols-12 gap-12 mt-5">
    <div class="intro-y col-span-12 lg:col-span-12">
        <form method="post" action="{{ route('admin.nganh.update', $nganh->id) }}">
            @csrf
            @method('patch')
            <div class="intro-y box p-5">
                <div>
                    <label for="title" class="form-label">Tên ngành</label>
                    <input id="title" name="title" type="text" value="{{ $nganh->title }}" class="form-control" placeholder="Tên ngành" required>
                </div>
                <div class="mt-3">
                    <label for="status" class="form-select-label">Tình trạng</label>
                    <select name="status" class="form-select mt-2" required>
                        <option value="active" {{ $nganh->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $nganh->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="text-right mt-5">
                    <button type="submit" class="btn btn-primary w-24">Cập nhật</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection