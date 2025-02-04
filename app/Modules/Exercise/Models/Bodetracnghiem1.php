<?php
namespace App\Modules\Exercise\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Exercise\Models\HocPhan; // Import model Module
use App\Modules\Exercise\Models\TracNghiemCauhoi;
use App\Models\User; // Import model User

class Bodetracnghiem1 extends Model
{
    use HasFactory;

    protected $table = 'bode_tracnghiems';

    protected $fillable = [
        'title',
        'hocphan_id',
        'slug',
        'start_time',
        'end_time',
        'time',
        'tags',
        'user_id',
        'total_points',
        'questions',
    ];

    protected $casts = [
        'questions' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function hocphan()
    {
        return $this->belongsTo(HocPhan::class, 'hocphan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
/**
     * Lấy danh sách các TracNghiemCauhoi dựa vào JSON questions.
     */
    public function tracnghiemCauhois()
    {
        $questionIds = collect($this->questions)->pluck('id_question'); // Lấy danh sách id_question từ JSON
        return TracNghiemCauhoi::whereIn('id', $questionIds)->get();
    }

    public function distributePoints()
    {
        $questions = $this->questions; // Lấy danh sách câu hỏi từ trường `questions`
        $numQuestions = count($questions);

        if ($numQuestions > 0) {
            $pointsPerQuestion = $this->total_points / $numQuestions; // Điểm cho mỗi câu hỏi
            $this->questions = collect($questions)->map(function ($id_question) use ($pointsPerQuestion) {
                return [
                    'id_question' => $id_question,
                    'points' => round($pointsPerQuestion, 2), // Làm tròn đến 2 chữ số thập phân
                ];
            })->toArray();

            $this->save(); // Lưu lại bộ đề với thông tin cập nhật
        }
    }
    public static function createNextBode($hocphan_id, $total_points,$user_id)
    {
        // Lấy số thứ tự tiếp theo của bộ đề
        $latestBode = Bodetracnghiem1::where('hocphan_id', $hocphan_id)->orderBy('id', 'desc')->first();
        $nextIndex = $latestBode ? $latestBode->id + 1 : 1;

        // Tạo thông tin bộ đề
        $title = "Bộ đề tự động #" . $nextIndex;
        $slug = "bo-de-tu-dong-" . $nextIndex . "-" . time();
        $start_time = now(); // Thời gian bắt đầu là hiện tại
        $end_time = $start_time->copy()->addHours(2); // Thời gian kết thúc
        $time = 120; // Thời gian làm bài (phút)
        $tags = "Tự động, Bộ đề #" . $nextIndex;
        $user_id = 1; // ID người tạo (có thể thay đổi)

        // Lấy danh sách loai_id có câu hỏi thuộc hocphan_id
        $loaiIds = TracNghiemCauhoi::where('hocphan_id', $hocphan_id)
            ->distinct()
            ->pluck('loai_id')
            ->toArray();

        $questions = [];

        // Duyệt qua từng loai_id và lấy 10 câu hỏi ngẫu nhiên
        foreach ($loaiIds as $loaiId) {
            $randomQuestions = TracNghiemCauhoi::where('hocphan_id', $hocphan_id)
                ->where('loai_id', $loaiId)
                ->inRandomOrder()
                ->take(10)
                ->pluck('id')
                ->toArray();

            $questions = array_merge($questions, $randomQuestions);
        }

        // Tính điểm cho từng câu hỏi
        $numQuestions = count($questions);
        $questionsWithPoints = [];

        if ($numQuestions > 0) {
            $pointsPerQuestion = $total_points / $numQuestions; // Điểm cho mỗi câu hỏi

            foreach ($questions as $id_question) {
                $questionsWithPoints[] = [
                    'id_question' => $id_question,
                    'points' => round($pointsPerQuestion, 2), // Làm tròn đến 2 chữ số
                ];
            }
        }

        // Tạo bộ đề
        $bode = Bodetracnghiem1::create([
            'title' => $title,
            'hocphan_id' => $hocphan_id,
            'slug' => $slug,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'time' => $time,
            'tags' => $tags,
            'user_id' => $user_id,
            'total_points' => $total_points,
            'questions' => $questionsWithPoints, // Lưu danh sách câu hỏi và điểm tương ứng
        ]);

        return $bode; // Trả về bộ đề vừa tạo
    }
}