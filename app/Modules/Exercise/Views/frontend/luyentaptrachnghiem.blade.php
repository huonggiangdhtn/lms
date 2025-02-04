@extends('Tuongtac::frontend.blogs.body')
@section('topcss')
    <meta name="csrf-token" content="{{ csrf_token() }}">
 
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <style>
        .list-inline-item {
            margin-right: 10px;
        }

        

        .question-button.btn-success {
            font-weight: bold;
            color: white;
        }
        #question-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start; /* Căn trái */
            gap: 10px; /* Khoảng cách giữa các phần tử */
        }

        .question-button {
            width: auto;
            height: auto;
            padding: 10px 15px;
            border-radius: 50%;
            font-weight: bold;
            text-align: center;
        }
    </style>
@endsection
@section('inner-content')
<section style="margin-top:10px; margin-bottom:10px ">
    <div class='container'>
        <div class="timer mt-3 text-right">
            <h4>Thời gian còn lại: <span id="timer">{{$bode->time}}:00</span></h4>
            <p> Chú ý khi làm bài không chuyển tab không làm việc trên các cửa sổ khác! </p>
        </div>
      
            <div class="container mt-3">
                <h4>Danh sách câu hỏi</h4>
                <ul id="question-list" class="list-inline">
                    @foreach ($questions as $index => $question)
                        <li class="list-inline-item">
                            <button
                                class="btn btn-outline-primary question-button"
                                data-index="{{ $index }}"
                                id="question-button-{{ $index }}">
                                {{ $index + 1 }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        <form action="{{ route('front.tracnghiem.submit', $bode->id) }}" method="POST">
            @csrf
            <input type='hidden' name='time_taken' id ='time_taken' />
            <div id="questions-container" class="mt-4">
                @foreach ($questions as $index => $question)
                    <div class="question" data-index="{{ $index }}" style="{{ $index == 0 ? '' : 'display: none;' }}">
                        <div class="card mb-3">
                            <div class="card-body">
                                <p class="card-title">Câu {{ $index + 1 }}: {!! $question->content !!}</p>
                                <div class="options">
                                    @foreach ($question->answers as $answer)
                                        <div class="form-check">
                                            <input
                                                class="form-check-input question-answer"
                                                type="radio"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ $answer->id }}"
                                                id="answer-{{ $answer->id }}"
                                                data-question-id="{{ $index }}">
                                            <label class="form-check-label" for="answer-{{ $answer->id }}">
                                                {{ $answer->content }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="navigation-buttons mt-3">
                <button type="button" id="prev-button" class="btn btn-secondary" style="display: none;">Quay lại</button>
                <button type="button" id="next-button" class="btn btn-primary">Câu tiếp theo</button>
                <button type="submit" id="submit-button" class="btn btn-success"  >Nộp bài</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('botscript')
 
 <script>
let isSubmitting = false;

// Đánh dấu trước khi submit
document.querySelector("form").addEventListener("submit", function () {
    isSubmitting = true; // Đánh dấu form đang được submit
});

// Đảm bảo trạng thái submit khi bấm nút submit
// document.querySelector("form button[type='submit']").addEventListener("mousedown", function () {
//     isSubmitting = true;
// });

// Xử lý onblur
window.onblur = function () {
    setTimeout(function () {
        if (!isSubmitting) {
            alert("Bạn không được rời khỏi trang trong khi làm bài!");
            location.reload();
        }
    }, 10); // Trì hoãn 10ms
};
</script>

<script>
      

      document.addEventListener("DOMContentLoaded", function () {
            // Cài đặt thời gian thi (ví dụ: 10 phút)
            const totalTime = {{$bode->time}} * 60; // Thời gian làm bài (tổng thời gian), tính bằng giây
            let timeLimit = totalTime; // Biến đếm ngược
            const timerElement = document.getElementById("timer");

            // Tạo input ẩn để lưu thời gian làm bài
            const hiddenInput = document.getElementById("time_taken");
            // hiddenInput.type = "hidden";
            // hiddenInput.name = "time_taken"; // Tên biến gửi đến server
            // document.querySelector("form").appendChild(hiddenInput);

            // Hàm cập nhật đồng hồ
            function updateTimer() {
                const minutes = Math.floor(timeLimit / 60);
                const seconds = timeLimit % 60;
                timerElement.textContent = `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
                const timeTaken = totalTime - timeLimit; // Thời gian làm bài
                hiddenInput.value = timeTaken; // Lưu giá trị vào input ẩn
            }

            // Đếm ngược
            const countdown = setInterval(function () {
                if (timeLimit <= 0) {
                    clearInterval(countdown);
                    alert("Hết thời gian! Bài thi sẽ được nộp.");

                    // Tính thời gian làm bài = tổng thời gian - thời gian còn lại
                    const timeTaken = totalTime - timeLimit;
                    hiddenInput.value = timeTaken;

                    document.querySelector("form").submit(); // Tự động nộp bài
                } else {
                    timeLimit--;
                    updateTimer();
                }
            }, 1000);

            // Hiển thị thời gian ban đầu
            updateTimer();

            // Cập nhật thời gian làm bài khi người dùng submit thủ công
            document.querySelector("form").addEventListener("submit", function () {
                const timeTaken = totalTime - timeLimit; // Thời gian làm bài
                hiddenInput.value = timeTaken; // Lưu giá trị vào input ẩn
                alert(timeTaken);
            });
        });


      

        document.addEventListener('DOMContentLoaded', function () {
        const questions = document.querySelectorAll('.question');
        const questionButtons = document.querySelectorAll('.question-button');
        const prevButton = document.getElementById('prev-button');
        const nextButton = document.getElementById('next-button');
        const submitButton = document.getElementById('submit-button');
        const answers = document.querySelectorAll('.question-answer');
        let currentIndex = 0;

        function showQuestion(index) {
            questions.forEach((q, i) => {
                q.style.display = i === index ? '' : 'none';
            });

            questionButtons.forEach((btn, i) => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
                if (i === index) {
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-primary');
                }
            });

            // Hiển thị hoặc ẩn các nút điều hướng
            prevButton.style.display = index > 0 ? '' : 'none';
            nextButton.style.display = index < questions.length - 1 ? '' : 'none';
            // submitButton.style.display = index === questions.length - 1 ? '' : 'none';
        }

        // Cập nhật trạng thái câu hỏi được trả lời
        answers.forEach(answer => {
            answer.addEventListener('change', function () {
                const questionId = this.getAttribute('data-question-id');
                const button = document.getElementById(`question-button-${questionId}`);
                button.classList.add('btn-success'); // Đổi màu nếu câu hỏi đã trả lời
                button.style.fontWeight = 'bold'; // Bôi đậm
            });
        });

        // Xử lý khi nhấn vào số thứ tự câu hỏi
        questionButtons.forEach((button, index) => {
            button.addEventListener('click', function () {
              
                currentIndex = index;
                showQuestion(currentIndex);
                // alert('qe');
            });
        });

        // Xử lý nút "Câu tiếp theo"
        nextButton.addEventListener('click', function () {
            if (currentIndex < questions.length - 1) {
                currentIndex++;
                showQuestion(currentIndex);
            }
        });

        // Xử lý nút "Quay lại"
        prevButton.addEventListener('click', function () {
            if (currentIndex > 0) {
                currentIndex--;
                showQuestion(currentIndex);
            }
        });

        // Hiển thị câu hỏi đầu tiên
        showQuestion(currentIndex);
    });
</script>
<script>
  
</script>
 
@endsection