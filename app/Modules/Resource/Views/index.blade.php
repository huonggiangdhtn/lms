@extends('backend.layouts.master')

@section('content')
    <h2 class="intro-y text-lg font-medium mt-10">
        Danh sách tài nguyên
    </h2>

    <!-- Nút thêm tài nguyên -->
    <div class="intro-y flex items-center mt-5">
        <a href="{{ route('admin.resources.create') }}" class="btn btn-primary shadow-md mr-2">Thêm tài nguyên</a>
    </div>

    <!-- Danh sách tài nguyên -->
    <div class="grid grid-cols-12 gap-6 mt-5">
        @if (isset($resources) && count($resources) > 0)
            @foreach ($resources as $resource)
                @php
                    $fileType = $resource->file_type;
                @endphp

                <div class="intro-y col-span-6 sm:col-span-4 lg:col-span-3">
                    <div class="card p-4 border rounded shadow-md relative">
                        <a href="{{ route('admin.resources.show', $resource->slug) }}" target="_blank">
                            <div class="relative">
                                @switch(true)
                                    @case(strpos($fileType, 'image/') === 0)
                                        <!-- Hiển thị ảnh -->
                                        <img src="{{ $resource->url }}" alt="{{ $resource->title }}"
                                            class="w-full h-40 object-cover rounded resource-image" />
                                    @break

                                    @case(strpos($fileType, 'video/') === 0)
                                        <!-- Hiển thị video -->
                                        <video controls class="w-full h-40 object-cover rounded resource-video">
                                            <source src="{{ $resource->url }}" type="{{ $fileType }}">
                                            Trình duyệt của bạn không hỗ trợ thẻ video.
                                        </video>
                                    @break

                                    @case(strpos($fileType, 'audio/') === 0)
                                        <!-- Hiển thị audio -->
                                        <audio controls class="w-full rounded resource-audio">
                                            <source src="{{ $resource->url }}" type="{{ $fileType }}">
                                            Trình duyệt của bạn không hỗ trợ thẻ audio.
                                        </audio>
                                    @break

                                    @case($fileType === 'application/pdf')
                                        <!-- Hiển thị file PDF -->
                                        <embed src="{{ $resource->url }}" type="application/pdf"
                                            class="w-full h-40 object-cover rounded resource-pdf" />
                                    @break

                                    @case(in_array($fileType, [
                                            'application/msword',
                                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        ]))
                                        <!-- Hiển thị file Word -->
                                        <img src="{{ asset('assets/icons/word-icon.png') }}" alt="{{ $resource->title }}"
                                            class="w-full h-40 object-cover rounded resource-doc" />
                                    @break

                                    @case(in_array($fileType, [
                                            'application/vnd.ms-powerpoint',
                                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                        ]))
                                        <!-- Hiển thị file PowerPoint -->
                                        <img src="{{ asset('assets/icons/ppt-icon.png') }}" alt="{{ $resource->title }}"
                                            class="w-full h-40 object-cover rounded resource-ppt" />
                                    @break

                                    @case(in_array($fileType, [
                                            'application/vnd.ms-excel',
                                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        ]))
                                        <!-- Hiển thị file Excel -->
                                        <img src="{{ asset('assets/icons/excel-icon.png') }}" alt="{{ $resource->title }}"
                                            class="w-full h-40 object-cover rounded resource-excel" />
                                    @break

                                    @default
                                        <!-- Hiển thị các file khác -->
                                        <img src="{{ asset('assets/icons/default-file-icon.png') }}" alt="{{ $resource->title }}"
                                            class="w-full h-40 object-cover rounded resource-default" />
                                @endswitch

                                <div
                                    class="image-title absolute inset-x-0 bottom-0 p-2 text-black bg-white bg-opacity-80 text-center rounded-b">
                                    {{ $resource->title }}
                                    @if ($fileType === 'application/pdf')
                                        (PDF)
                                    @elseif (in_array($fileType, [
                                            'application/msword',
                                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        ]))
                                        (Word)
                                    @elseif (in_array($fileType, [
                                            'application/vnd.ms-powerpoint',
                                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                        ]))
                                        (PowerPoint)
                                    @elseif (in_array($fileType, [
                                            'application/vnd.ms-excel',
                                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        ]))
                                        (Excel)
                                    @endif
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

    <!-- Modal -->
    <div class="modal fade" id="resourceModal" tabindex="-1" aria-labelledby="resourceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resourceModalLabel">Chi tiết tài nguyên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3 id="modalResourceTitle"></h3>
                    <div id="modalResourceContent"></div>
                    <p id="modalFileType"></p>
                    <p id="modalFileSize"></p>
                    <p id="modalFileUrl"></p>
                </div>
            </div>
        </div>
    </div>


    {{ $resources->links() }} <!-- Phân trang -->

@endsection
