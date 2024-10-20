@extends('backend.layouts.master')

@section('content')
    <h2 class="intro-y text-lg font-medium mt-10">
        Danh sách danh mục bài viết
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="{{ route('admin.blogcategory.create') }}" class="btn btn-primary shadow-md mr-2">Thêm danh mục</a>
            <div class="hidden md:block mx-auto text-slate-500">Hiển thị trang {{ $blogcats->currentPage() }} trong
                {{ $blogcats->lastPage() }} trang</div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <form action="{{ route('admin.blogcategory.search') }}" method="get">
                        @csrf
                        <input type="text" name="datasearch" class="ipsearch form-control w-56 box pr-10"
                            placeholder="Tìm kiếm...">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </form>
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">TÊN</th>
                        <th class="whitespace-nowrap">ẢNH</th>
                        <th class="text-center whitespace-nowrap">TRẠNG THÁI</th>
                        <th class="text-center whitespace-nowrap">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blogcats as $item)
                        <tr class="intro-x">
                            <td>
                                <a class="font-medium whitespace-nowrap">{{ $item->title }}</a>
                            </td>
                            <td class="w-40">
                                <div class="flex">
                                    <div class="w-10 h-10 image-fit zoom-in">
                                        <img class="tooltip rounded-full" src="{{ asset($item->photo) }}"
                                            alt="{{ $item->title }}">
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" data-toggle="switchbutton" data-onlabel="active"
                                    data-offlabel="inactive" {{ $item->status == 'active' ? 'checked' : '' }} data-size="sm"
                                    name="toggle" value="{{ $item->id }}" data-style="ios">
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a href="{{ route('admin.blogcategory.edit', $item->id) }}"
                                        class="flex items-center mr-3">
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.blogcategory.destroy', $item->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <a class="flex items-center text-danger dltBtn" data-id="{{ $item->id }}"
                                            href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#delete-confirmation-modal">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Xóa
                                        </a>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- END: HTML Table Data -->
    <!-- BEGIN: Pagination -->
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
        <nav class="w-full sm:w-auto sm:mr-auto">
            {{ $blogcats->links('vendor.pagination.tailwind') }}
        </nav>
    </div>
    <!-- END: Pagination -->
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.dltBtn').click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Bạn có chắc muốn xóa không?',
                text: "Bạn không thể lấy lại dữ liệu sau khi xóa",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Vâng, tôi muốn xóa!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $("[name='toggle']").change(function() {
            var mode = $(this).prop('checked');
            var id = $(this).val();
            $.ajax({
                url: "{{ route('admin.blogcategory.status') }}",
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    mode: mode,
                    id: id,
                },
                success: function(response) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response.msg,
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            });
        });
    </script>
@endsection
