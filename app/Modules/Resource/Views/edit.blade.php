@extends('backend.layouts.master')

@section('scriptop')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/js/tom-select.complete.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('js/css/tom-select.min.css') }}">
@endsection

@section('content')

<div class="intro-y flex items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Chỉnh sửa tài nguyên
    </h2>
</div>
<div class="grid grid-cols-12 gap-12 mt-5">
    <div class="intro-y col-span-12 lg:col-span-12">
        <!-- BEGIN: Form Layout -->
        <form method="post" action="{{ route('admin.resources.update', $resource->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="intro-y box p-5">
                <div>
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input id="title" name="title" type="text" class="form-control" placeholder="Nhập tiêu đề" value="{{ $resource->title }}" required>
                </div>

                <div class="mt-3">
                    <label for="resource_type_id" class="form-label">Loại tài nguyên</label>
                    <select name="resource_type_id" class="form-select mt-2">
                        @foreach($resourceTypes as $type)
                        <option value="{{ $type->id }}" {{ $resource->resource_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->title }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-3">
                    <label for="resource_link_type_id" class="form-label">Loại liên kết tài nguyên</label>
                    <select name="resource_link_type_id" class="form-select mt-2">
                        @foreach($linkTypes as $type)
                        <option value="{{ $type->id }}" {{ $resource->resource_link_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->title }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-3">
                    <label for="file" class="form-label">Tệp phương tiện (tùy chọn)</label>
                    <input type="file" name="file" class="form-control">
                </div>

                <div class="mt-3">
                    <label for="tags" class="form-label">Tags</label>
                    <input id="tags" name="tags" type="text" class="form-control" placeholder="Nhập tags, cách nhau bằng dấu phẩy" value="{{ old('tags') }}">
                </div>

                <div class="text-right mt-5">
                    <button type="submit" class="btn btn-primary w-24">Cập nhật</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var select = new TomSelect('#tags', {
        create: true,
        plugins: ['remove_button'],
        onItemAdd: function(value) {
            this.setTextboxValue('');
            this.refreshOptions();
        }
    });
</script>
@endsection
