@extends('frontend.layouts.master')
@section('head_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
   
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
@endsection
@section('content')
<section style="margin-top:20px; margin-bottom:10px">
 
    <div class="container">
        <div class="row">
            <!-- Lặp qua từng học phần -->
            @foreach ($hocphans as $hocphan)
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <img class="card-img-top" src="{{ $hocphan->photo }}" alt="Hình ảnh học phần">
                        <div class="card-body">
                            <h5 class="card-title">{{ $hocphan->title }}</h5>
                            <p class="card-text">{{ $hocphan->summary }}</p>
                            <ul class="list-unstyled mb-3">
                                <li><strong>Mã học phần:</strong> {{ $hocphan->code }}</li>
                                <li><strong>Tín chỉ:</strong> {{ $hocphan->tinchi }}</li>
                                <li><strong>Hình thức thi:</strong> {{ $hocphan->hinhthucthi }}</li>
                            </ul>
                            <a href="{{ route('front.hocphan.dangky', ['hocphan_id' => $hocphan->id]) }}"  style="padding:5px" class="btn btn-medium btn-switch-text btn-dark-gray btn-round-edge">
                                Vào học
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
</section>
@endsection

 