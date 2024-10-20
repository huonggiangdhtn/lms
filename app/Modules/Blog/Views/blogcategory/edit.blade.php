@extends('backend.layouts.master')

@section('scriptop')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/js/dropzone.js') }}"></script>
@endsection

@section('content')
    <h2 class="intro-y text-lg font-medium mt-10">Sửa danh mục bài viết</h2>
    <form action="{{ route('admin.blogcategory.update', $blogcat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12">
                <div class="mt-5">
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="{{ $blogcat->title }}" required>
                </div>
                <div class="mt-5">
                    <label class="form-label">Ảnh</label>
                    <div class="dropzone" id="mydropzone" url="{{ route('admin.upload.avatar') }}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        <div class="dz-message" data-dz-message>
                            <div class="font-medium">Kéo thả hoặc chọn ảnh.</div>
                        </div>
                    </div>
                    <input type="hidden" id="photo" name="photo" value="{{ $blogcat->photo }}">
                    <div class="mt-3">
                        <?php $photos = explode(',', $blogcat->photo); ?>
                        <div class="grid grid-cols-10 gap-5">
                            @foreach ($photos as $photo)
                                <div data-photo="{{ $photo }}"
                                    class="product_photo col-span-5 md:col-span-2 h-28 relative image-fit cursor-pointer zoom-in">
                                    <img class="rounded-md" src="{{ $photo }}">
                                    <div title="Xóa hình này?"
                                        class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white bg-danger right-0 top-0 -mr-2 -mt-2">
                                        <i data-lucide="x" class="btn_remove w-4 h-4"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" class="form-control" required>
                        <option value="active" {{ $blogcat->status == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                        <option value="inactive" {{ $blogcat->status == 'inactive' ? 'selected' : '' }}>Không kích hoạt
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <button type="submit" class="btn btn-success">Cập nhật</button>
            <a href="{{ route('admin.blogcategory.index') }}" class="btn btn-secondary">Trở lại</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        Dropzone.options.mydropzone = {
            url: "{{ route('admin.upload.avatar') }}",
            maxFilesize: 1, // MB
            acceptedFiles: "image/jpeg,image/png,image/gif",
            success: function(file, response) {
                if (response.status == "true") {
                    var currentPhotos = $('#photo').val();
                    if (currentPhotos) {
                        currentPhotos += ",";
                    }
                    currentPhotos += response.link; // Link returned from server
                    $('#photo').val(currentPhotos);
                    $('#mydropzone').append(
                        '<div class="product_photo col-span-5 md:col-span-2 h-28 relative image-fit cursor-pointer zoom-in"><img class="rounded-md" src="' +
                        response.link +
                        '"><div title="Xóa hình này?" class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white bg-danger right-0 top-0 -mr-2 -mt-2"><i data-lucide="x" class="btn_remove w-4 h-4"></i></div></div>'
                        );
                }
            },
            removedfile: function(file) {
                var oldPhotos = $('#photo').val().split(',');
                oldPhotos = oldPhotos.filter(function(photo) {
                    return photo !== file.name; // Remove the deleted photo
                });
                $('#photo').val(oldPhotos.join(','));
            }
        };

        $(".btn_remove").click(function() {
            $(this).closest('.product_photo').remove();
            var link_photo = "";
            $('.product_photo').each(function() {
                if (link_photo != '') {
                    link_photo += ',';
                }
                link_photo += $(this).data("photo");
            });
            $('#photo').val(link_photo);
        });
    </script>
@endsection
