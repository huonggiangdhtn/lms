@extends('Tuongtac::frontend.blogs.body')
@section('topcss')
   
@endsection
@section('inner-content')
<section style="margin-top:10px; margin-bottom:10px">
 
    <div class="container">
      
            <h2>Kết Quả Làm Bài</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p><strong>Bộ đề:</strong> {{ $bode->title }}</p>
                    <p><strong>Thời gian làm bài:</strong> {{ $enrolltracnghiem->time }} phút</p>
                    <p><strong>Tổng điểm:</strong> {{ $enrolltracnghiem->point }} / {{ $bode->total_points }}</p>
                    {{-- <p><strong>Số câu trả lời:</strong> {{ $correctAnswers }} / {{ count($questions) }}</p> --}}
                </div>
            </div>
        
             
             
            <div class="mt-4 text-center">
                <a href="{{ route('front.tracnghiem.share', $bode->id) }}" class="btn btn-primary">Chia sẻ</a>
                <a href="{{ route('front.enroll.index',$hocphan->id) }}" class="btn btn-secondary">Quay Lại Luyện Tập</a>
            </div>
        
       
        
    </div>
    
    
</section>
@endsection

 