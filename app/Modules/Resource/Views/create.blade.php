@extends('backend.layouts.master')
@section ('scriptop')

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="{{asset('js/js/tom-select.complete.min.js')}}"></script>
<link rel="stylesheet" href="{{ asset('/js/css/tom-select.min.css') }}">
@endsection

@section('content')

    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Thêm tài nguyên
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-12 mt-5">
        <div class="intro-y col-span-12 lg:col-span-12">
            <!-- BEGIN: Form Layout -->
            <form method="post" action="{{route('admin.resources.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="intro-y box p-5">
                    <div>
                        <label for="regular-form-1" class="form-label">Tiêu đề</label>
                        <input id="title" name="title" type="text" class="form-control" placeholder="Nhập tiêu đề" value="{{ old('title') }}" required>
                    </div>

                    <div class="mt-3">
                        <label for="" class="form-label">Loại tài nguyên</label>
                        <select name="resource_type_id" class="form-select mt-2">
                            <option value="">- Chọn loại tài nguyên -</option>
                            @foreach($resourceTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3">
                        <label for="" class="form-label">Loại liên kết tài nguyên</label>
                        <select name="resource_link_type_id" class="form-select mt-2">
                            <option value="">- Chọn loại liên kết -</option>
                            @foreach($linkTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3">
                        <label for="" class="form-label">Mô tả tài nguyên</label>
                        <textarea class="editor" name="description" id="editor2">{{ old('description') }}</textarea>
                    </div>

                    <div class="mt-3">
                        <label for="" class="form-label">Tệp phương tiện</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>

                    <div class="mt-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input id="tags" name="tags" type="text" class="form-control" placeholder="Nhập tags, cách nhau bằng dấu phẩy" value="{{ old('tags') }}">
                    </div>

                    <div class="text-right mt-5">
                        <button type="submit" class="btn btn-primary w-24">Lưu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section ('scripts')

<script>
    var select = new TomSelect('#select-junk',{
        maxItems: null,
        allowEmptyOption: true,
        plugins: ['remove_button'],
        sortField: {
            field: "text",
            direction: "asc"
        },
        onItemAdd:function(){
                this.setTextboxValue('');
                this.refreshOptions();
            },
        create: true
    });
    select.clear();
</script>

<script src="{{asset('js/js/ckeditor.js')}}"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor2'), {
            ckfinder: {
                uploadUrl: '{{route("admin.upload.ckeditor")."?_token=".csrf_token()}}'
            },
            mediaEmbed: { previewsInData: true }
        })
        .then(editor => {
            console.log(editor);
        })
        .catch(error => {
            console.error(error);
        });
</script>

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
