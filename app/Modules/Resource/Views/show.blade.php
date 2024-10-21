@extends('backend.layouts.master')

@section('content')
<div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Chi tiết tài nguyên</h1>
    
    <h3 class="text-xl font-semibold mb-2">{{ $resource->title }}</h3>
    
    <div class="mb-4">
        @if (strpos($resource->file_type, 'image/') === 0)
            <img src="{{ $resource->url }}" alt="{{ $resource->title }}" class="max-w-full h-auto rounded shadow-lg" />
        @elseif (strpos($resource->file_type, 'video/') === 0)
            <video controls class="max-w-full h-auto rounded shadow-lg">
                <source src="{{ $resource->url }}" type="{{ $resource->file_type }}">
                Trình duyệt của bạn không hỗ trợ video.
            </video>
        @elseif (strpos($resource->file_type, 'audio/') === 0)
            <audio controls class="max-w-full h-auto rounded shadow-lg">
                <source src="{{ $resource->url }}" type="{{ $resource->file_type }}">
                Trình duyệt của bạn không hỗ trợ audio.
            </audio>
        @elseif ($resource->file_type === 'application/pdf')
            <embed src="{{ $resource->url }}" type="application/pdf" class="max-w-full h-auto rounded shadow-lg" />
        @else
            <p class="text-red-600">Không thể hiển thị tài liệu này.</p>
        @endif
    </div>
    
    <div class="mb-4">
        <p class="font-medium">File type: <span class="font-normal">{{ $resource->file_type }}</span></p>
        <p class="font-medium">File size: <span class="font-normal">{{ $resource->file_size }} bytes</span></p>
        <p class="font-medium">URL: <a href="{{ $resource->url }}" target="_blank" class="text-blue-500 underline">{{ $resource->url }}</a></p>
        <p class="font-medium">Tags: <span class="font-normal">{{ $resource->tags }}</span></p>
    </div>
    
    <div class="flex space-x-2">
        <a href="{{ route('admin.resources.edit', $resource->id) }}" class="flex items-center">
            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i>
            Chỉnh sửa
        </a> 
        
        <form action="{{ route('admin.resources.destroy', $resource->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <a class="flex items-center text-danger dltBtn" data-id="{{ $resource->id }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal">
                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                Xóa
            </a> 
        </form>
        
        <a href="{{ route('admin.resources.index') }}" class="flex items-center text-secondary">
            <i data-lucide="arrow-left-circle" class="w-4 h-4 mr-1"></i> 
            Quay lại danh sách
        </a>
        
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('.dltBtn').click(function(e) {
        var form = $('#delete-form-' + $(this).data('id'));
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
                Swal.fire(
                    'Đã xóa!',
                    'Tài nguyên của bạn đã được xóa.',
                    'success'
                );
            }
        });
    });
</script>
@endsection
