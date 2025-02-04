@extends('Tuongtac::frontend.blogs.body')
 
@section('inner-content')
<section style="margin-top:10px; margin-bottom:10px">
 
    <div class="container">
        <h3>Thông Tin Luyện Tập {{auth()->user()->full_name}}</h3>
        <div class="card">
            <div class="card-body">
                <p> <h5 class="card-title">Tổng Quan</h5> </p>
                <p><strong>Thời gian đã làm:</strong> {{ $enrollment->timespending }} phút / {{$hocphan->time_point}}</p>
                <p><strong>Số điểm trắc nghiệm đã đạt:</strong> {{ $enrollment->tracnghiem_point }} điểm / {{$hocphan->tracnghiem_point}}</p>
                @if(isset($certificate))
                    <a href="{{route('front.hocphan.certificate',$certificate)}}">Giấy chứng nhận </a>
                @endif
           
            </div>
        </div>
    
        <h3 class="mt-4">Kết Quả Các Bộ Đề</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Bộ Đề</th>
                        <th>Điểm Đạt</th>
                        <th>Thời Gian</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($enroll_tracnghiem as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->bode->title }}</td>
                            <td>{{ $item->point }} điểm</td>
                            <td>{{ $item->time}} phút</td>
                          
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    
        <div class="text-center mt-4">
            <a href="{{ route('front.tracnghiem.practice', ['id' => $enrollment->phancong_id]) }}" class="btn btn-primary">
                Vào Luyện Tập
            </a>
        </div>
    </div>
    
    
</section>
@endsection

@section('botscript')
 
 
 
 
@endsection