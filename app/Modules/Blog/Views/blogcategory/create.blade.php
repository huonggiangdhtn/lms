@extends('backend.layouts.master')

@section('scriptop')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/js/dropzone.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('/js/css/dropzone.min.css') }}">
@endsection

@section('content')
    <h2 class="intro-y text-lg font-medium mt-10">Thêm danh mục bài viết</h2>
    <form action="{{ route('admin.blogcategory.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12">
                <div class="mt-5">
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mt-5">
                    <label class="form-label">Ảnh</label>
                    <div class="px-4 pb-4 mt-5 flex items-center cursor-pointer relative">
                        <div data-single="true" id="mydropzone" class="dropzone" url="{{ route('admin.upload.avatar') }}">
                            <div class="fallback">
                                <input name="file" type="file" />
                            </div>
                            <div class="dz-message" data-dz-message>
                                <div class="font-medium">Kéo thả hoặc chọn ảnh.</div>
                            </div>
                        </div>
                        <input type="hidden" id="photo" name="photo" />
                    </div>
                </div>
                <div class="mt-5">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" class="form-control" required>
                        <option value="active">Kích hoạt</option>
                        <option value="inactive">Không kích hoạt</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('admin.blogcategory.index') }}" class="btn btn-secondary">Trở lại</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("#mydropzone", {
            url: "{{ route('admin.upload.avatar') }}",
            maxFilesize: 1, // MB
            acceptedFiles: "image/jpeg,image/png,image/gif",
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                if (response.status == "true") {
                    $('#photo').val(response.link);
                }
            },
            removedfile: function(file) {
                $('#photo').val('');
            },
            error: function(file, message) {
                console.log('Error:', message);
            }
        });
    </script>
@endsection
