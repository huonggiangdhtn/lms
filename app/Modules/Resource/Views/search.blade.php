@extends('backend.layouts.master')

@section('content')
    <h2 class="intro-y text-lg font-medium mt-10">
        Kết quả tìm kiếm cho "{{ $searchdata }}"
    </h2>

    <!-- Thanh công cụ cho tìm kiếm -->
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <a href="{{ route('admin.resources.create') }}" class="btn btn-primary shadow-md mr-2">Thêm tài nguyên</a>

        <div class="hidden md:block mx-auto text-slate-500">Hiển thị trang {{ $resources->currentPage() }} trong
            {{ $resources->lastPage() }} trang</div>
        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            <div class="w-56 relative text-slate-500">
                <form action="{{ route('admin.resources.search') }}" method="get">
                    @csrf
                    <input type="text" name="datasearch" class="ipsearch form-control w-56 box pr-10" value="{{ $searchdata }}" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </form>
            </div>
        </div>
    </div>

    <!-- Hiển thị kết quả tìm kiếm -->
    <div class="grid grid-cols-12 gap-6 mt-5">
        @if (isset($resources) && count($resources) > 0)
            @foreach ($resources as $resource)
                @php
                    $fileType = $resource->file_type;
                    $imageUrl = $resource->image_url;
                    $documentUrl = $resource->document_url;
                @endphp

                <div class="intro-y col-span-6 sm:col-span-4 lg:col-span-3">
                    <div class="card p-4 border rounded shadow-md relative">
                        <a href="{{ route('admin.resources.show', $resource->id) }}">
                            <div class="relative">
                                @if (!empty($imageUrl))
                                    <img src="{{ $imageUrl }}" alt="{{ $resource->title }}" class="w-full h-40 object-cover rounded resource-image" />
                                @elseif (!empty($documentUrl))
                                    <a href="{{ $documentUrl }}" class="text-blue-500 underline">
                                        Tải tài liệu
                                    </a>
                                @else
                                    @switch(true)
                                        @case(strpos($fileType, 'image/') === 0)
                                            <img src="{{ $resource->url }}" alt="{{ $resource->title }}" class="w-full h-40 object-cover rounded resource-image" />
                                        @break

                                        @case(strpos($fileType, 'video/') === 0)
                                            <video controls class="w-full h-40 object-cover rounded resource-video">
                                                <source src="{{ $resource->url }}" type="{{ $fileType }}">
                                                Trình duyệt của bạn không hỗ trợ thẻ video.
                                            </video>
                                        @break

                                        @case(!empty($resource->youtube_url) && (strpos($resource->youtube_url, 'youtube.com') !== false || strpos($resource->youtube_url, 'youtu.be') !== false))
                                            <iframe width="100%" height="160" src="{{ str_replace('watch?v=', 'embed/', $resource->youtube_url) }}" frameborder="0" allowfullscreen></iframe>
                                        @break

                                        @case(strpos($fileType, 'audio/') === 0)
                                            <audio controls class="w-full rounded resource-audio">
                                                <source src="{{ $resource->url }}" type="{{ $fileType }}">
                                                Trình duyệt của bạn không hỗ trợ thẻ audio.
                                            </audio>
                                        @break

                                        @case($fileType === 'application/pdf')
                                            <embed src="{{ $resource->url }}" type="application/pdf" class="w-full h-40 object-cover rounded resource-pdf" />
                                        @break

                                        @case(in_array($fileType, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']))
                                            <img src="{{ asset('assets/icons/icon1.png') }}" alt="{{ $resource->title }}" class="w-full h-40 object-cover rounded resource-doc" />
                                        @break

                                        @case(in_array($fileType, ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation']))
                                            <img src="{{ asset('assets/icons/icon1.png') }}" alt="{{ $resource->title }}" class="w-full h-40 object-cover rounded resource-ppt" />
                                        @break

                                        @case(in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']))
                                            <img src="{{ asset('assets/icons/icon1.png') }}" alt="{{ $resource->title }}" class="w-full h-40 object-cover rounded resource-excel" />
                                        @break

                                        @default
                                            <img src="{{ asset('assets/icons/icon1.png') }}" alt="{{ $resource->title }}" class="w-full h-40 object-cover rounded resource-default" />
                                    @endswitch
                                @endif

                                <div class="image-title absolute inset-x-0 bottom-0 p-2 text-black bg-white bg-opacity-80 text-center rounded-b">
                                    {{ $resource->title }}
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="intro-y col-span-12">
                <div class="text-center p-4">
                    <p class="text-lg text-red-600">Không tìm thấy tài nguyên nào!</p>
                </div>
            </div>
        @endif
    </div>

    {{ $resources->links() }}
@endsection