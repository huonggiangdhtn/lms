@extends('backend.layouts.master')
@section('content')

<div class="content">
 
    <h2 class="intro-y text-lg font-medium mt-10">
        Danh sách sinh vieen
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
           
            
            <div class="hidden md:block mx-auto text-slate-500">Hiển thị trang {{$enrolls->currentPage()}} trong {{$enrolls->lastPage()}} trang</div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            @if (Session::has('thongbao'))
                <div class="alert alert-success" id="success-alert">
                    {{ Session::get('thongbao') }}
                </div>
            @endif
            <script>
                // Kiểm tra xem thông báo có hiện diện không
                window.onload = function() {
                    var alert = document.getElementById('success-alert');
                    if (alert) {
                        // Đặt thời gian 4 giây trước khi ẩn
                        setTimeout(function() {
                            alert.style.display = 'none';
                        }, 4000); // 4000 ms = 4 giây
                    }
                };
            </script>
            <table class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">Tên</th>
                        <th class="whitespace-nowrap">Thời gian</th>                        
                        <th class="whitespace-nowrap">Điểm trắc nghiệm</th>
                        <th class="whitespace-nowrap">Điểm tự luận</th>
                        <th class="whitespace-nowrap">TRẠNG THÁI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrolls as $item)
                    <tr class="intro-x">
                        <td>
                            {{($item->user->full_name)}} 
                        </td>
                        
                        <td>
                            {{($item->timespending)}} 
                        </td>
                        <td>
                            {{($item->tracnghiem_point)}} 
                        </td>
                        <td>
                            {{($item->tuluan_point)}} 
                        </td>
                        <td>
                           
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
                {{$enrolls->links('vendor.pagination.tailwind')}}
            </nav>
           
        </div>
        <!-- END: Pagination -->
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{asset('backend/assets/vendor/js/bootstrap-switch-button.min.js')}}"></script>
<script>
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $('.dltBtn').click(function(e)
    {
        var form=$(this).closest('form');
        var dataID = $(this).data('id');
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
                // alert(form);
                form.submit();
                // Swal.fire(
                // 'Deleted!',
                // 'Your file has been deleted.',
                // 'success'
                // );
            }
        });
    });
</script>
<script>
    $(".ipsearch").on('keyup', function (e) {
        e.preventDefault();
        if (e.key === 'Enter' || e.keyCode === 13) {
           
            // Do something
            var data=$(this).val();
            var form=$(this).closest('form');
            if(data.length > 0)
            {
                form.submit();
            }
            else
            {
                  Swal.fire(
                    'Không tìm được!',
                    'Bạn cần nhập thông tin tìm kiếm.',
                    'error'
                );
            }
        }
    });
 
    
</script>
 
@endsection