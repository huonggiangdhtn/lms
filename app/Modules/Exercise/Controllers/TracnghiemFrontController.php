<?php

namespace App\Modules\Exercise\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Modules\Exercise\Models\Bodetracnghiem; 
use App\Modules\Exercise\Models\HocPhan;
use App\Models\User;
use App\Modules\Exercise\Models\TracNghiemCauhoi;
use App\Modules\Exercise\Models\EnrollmentTracnghiem;
use App\Modules\Exercise\Models\Certificate;
use App\Modules\Exercise\Models\Enrollment;
use App\Modules\Tuongtac\Models\TBlog;
use App\Modules\Tuongtac\Models\TPage;
class TracnghiemFrontController extends Controller
{
    protected $frontend;
   
    public function __construct( )
    {
        $this->frontend = env('FRONTEND');
        $this->frontend = 'frontend';
    }

    public function index()
    {
        

        // Truyền dữ liệu học phần tới view
        // return view('hocphan.index', compact('hocphans'));
        $data['detail'] = \App\Models\SettingDetail::find(1);  
        $data['categories'] = \App\Models\Category::where('status','active')->where('parent_id',null)->get();
        $user  = auth()->user();
         ////
        $data['pagetitle']="Danh sách các học phần"  ;
        $data['page_up_title'] = "Danh sách các học phần"  ;
        $data['page_subtitle']="Danh sách các học phần"  ;
        $data['page_title']= " " ;
        $data['hotbutton_title'] = "Doanh nghiệp gần bạn nhất"  ;
        $data['hotbutton_subtitle'] = "được xác nhận bởi itcctv";
        $data['hotbutton_link']= "";
        $data['page_up_title'] ="Danh sách các học phần"  ;
        $data['hocphans'] = HocPhan::all();
        return view('Exercise::'.$this->frontend.'.hocphan',$data);
    }
    public function sharecertificate(Request $request)
    {

        $fileController =new \App\Http\Controllers\FilesController();
        $imageData = $request->input('image'); // Base64 của ảnh
       
      

        if (!$imageData) {
            return response()->json(['message' => 'No image data received'], 400);
        }
        $bmiChartPath =  $fileController->saveBase64ImageToS3($imageData, 'certificate');

     

        // Xử lý Weight Chart
       
        $content = '<p>Giấy chứng nhận hoàn thành khoá học</p>'.
        '<img src="'.  $bmiChartPath  .'"/> <br/>' . 
        ' ';
        $tblog =new TBlog();
        $user = auth()->user();
        $title = 'Giấy chứng nhận hoàn thành khoá học của '. auth()->user()->full_name;
        $tblog = $tblog->add_tblog($title, $bmiChartPath  ,$content, auth()->id(),["giấy chứng nhận","luyện tập online","chứng chỉ online","học lập trình online"]);
        return response()->json([
            'url' => TPage::getPageUrl(  $user->id,'user'),
            'success' => true,
             
        ]);
        
 

    }
    public function share($bode_id)
    {
        // dd($request);
       
       
        // Lưu bài viết vào cơ sở dữ liệu
         // Xử lý BMI Chart
        $bode = Bodetracnghiem::find($bode_id);
        if (!$bode)
            return redirect()->back()->with('error','Không tìm thấy!');
        $hocphan = HocPhan::find($bode->hocphan_id);
        if (!$hocphan)
            return redirect()->back()->with('error','Không tìm thấy học phần!');
        $enroll_tracnghiem = EnrollmentTracnghiem::where('user_id',auth()->id())->where('bode_id',$bode->id)->first();
        $content = '<p>Kết quả học làm bài học phần:'.$hocphan->title.' </p>'.
        '<p> Điểm: '.$enroll_tracnghiem->point.' <p/>' . 
        '<p> Thời gian: '.$enroll_tracnghiem->time.' <p/> ' . 
        '<p> Hãy vào học phần <a href="'.route('front.enroll.index',$hocphan->id).'">'.$hocphan->title.' </a><p/> để luyện tập nhé!' 
        ;
        $tblog =new TBlog();
        $user = auth()->user();
        $title =  'Điểm học phần '.$hocphan->title.' của '. auth()->user()->full_name.': '.$enroll_tracnghiem->point;
        $tblog = $tblog->add_tblog($title, $hocphan->photo  ,$content, auth()->id(),["luyện tập online","chứng chỉ online","học lập trình online"]);
        return redirect( TPage::getPageUrl(  $user->id,'user'));
    }

    public function XemEnroll($id)
    {
        $enrollment = Enrollment::where('user_id',auth()->id())
        ->where('phancong_id', $id)->first();
        if (!$enrollment)
            return redirect()->back()->with('error','Không tìm thấy!');

        $enroll_tracnghiem = EnrollmentTracnghiem::where('enroll_id',$enrollment->id)->get();
        // $bodes = Bodetracnghiem::

        $data['detail'] = \App\Models\SettingDetail::find(1);  
        $data['categories'] = \App\Models\Category::where('status','active')->where('parent_id',null)->get();
        $user  = auth()->user();
         ////
         $hocphan = Hocphan::find($id);
        $data['pagetitle']="Thông tin khoá học ".$hocphan->title  ;
        $data['page_up_title'] = "Thông tin khoá học ".$hocphan->title  ;
        $data['page_subtitle']="Thông tin khoá học ".$hocphan->title  ;
        $data['page_title']= " " ;
        $data['hotbutton_title'] = "Doanh nghiệp gần bạn nhất"  ;
        $data['hotbutton_subtitle'] = "được xác nhận bởi itcctv";
        $data['hotbutton_link']= "";
        $data['page_up_title'] ="Thông tin khoá học ".$hocphan->title  ;
        $data['enrollment'] = $enrollment;
        $data['enroll_tracnghiem'] = $enroll_tracnghiem;
        $data['hocphan'] = $hocphan;
       
        if($hocphan->tracnghiem_point <= $enrollment->tracnghiem_point && $hocphan->tuluan_point <= $enrollment->trachnghiem_point 
        && $hocphan->time_point <= $enrollment->time_point)
        {
            $certificate = Certificate::where('user_id', auth()->id())
            ->where('hocphan_id', $hocphan->id)
            ->first();
            if(!$certificate)
            {
                $certificate = Certificate::create([
                    'user_id' => auth()->id(),
                    'certificate_number' => 'CER'.uniqid(),
                    'certificate_name' => $hocphan->title,
                    'hocphan_id' => $hocphan->id,
                    'issued_date' => now(),
                ]);
            }
           
           
        }

        $certificate = Certificate::where('user_id', auth()->id())
        ->where('hocphan_id', $hocphan->id)
        ->first();
        if ($certificate)
        {
            $data['certificate'] = $certificate;
        }
        return view('Exercise::'.$this->frontend.'.enrolldashboard',$data);
    }
    public function viewCertificate($id)
    {
        $data['certificate'] = Certificate::find($id);
        $data['user'] = User::find($data['certificate']->user_id);
        
        return view('Exercise::'.$this->frontend.'.certificate',$data);
        
    }
    public function Dangky($id)
    {
           // Kiểm tra xem user_id và phancong_id đã tồn tại trong bảng enrollments chưa
        if (!auth()->id())
        {
            return redirect()->route('front.login')->with('success','Hãy đăng nhập để đăng ký khoá học!');
        }
        $enrollment = Enrollment::where('user_id',auth()->id())
        ->where('phancong_id', $id)->first();
       

        if (!$enrollment) {
            // Nếu chưa tồn tại, thêm bản ghi mới
            $enrollment = Enrollment::create([
            'user_id' => auth()->id(),
            'phancong_id' => $id,
            'timespending' => 0,
            'tracnghiem_point' => 0,
            'tuluan_point' => 0,
            'time_point' => 0,
            'process' => 0,
            'status' => Enrollment::STATUS_SUCCESS, // Trạng thái mặc định
            ]);
        }
        return redirect()->route('front.enroll.index',$id);
       
    }
    public function Luyentaptracnghiem($phancong_id)
    {
         if (!auth()->id())
        {
            return redirect()->route('front.login')->with('success','Hãy đăng nhập để đăng ký khoá học!');
        }
        // Lấy thông tin enrollment
        $hocphan_id = $phancong_id;

        $bode = Bodetracnghiem::createNextBode($hocphan_id, 10,auth()->id());
        $data['bode'] = $bode;
        $hocphan =  Hocphan::find($hocphan_id);
        $data['hocphan'] = $hocphan;
        $data['questions'] = $bode->tracnghiemCauhois();

        $data['detail'] = \App\Models\SettingDetail::find(1);  
        $data['categories'] = \App\Models\Category::where('status','active')->where('parent_id',null)->get();
        $user  = auth()->user();
         ////
         
        $data['pagetitle']="Làm bài trắc nghiệm ".$hocphan->title  ;
        $data['page_up_title'] = "Làm bài trắc nghiệm ".$hocphan->title  ;
        $data['page_subtitle']="Làm bài trắc nghiệm ".$hocphan->title  ;
        $data['page_title']= " " ;
        $data['hotbutton_title'] = "Doanh nghiệp gần bạn nhất"  ;
        $data['hotbutton_subtitle'] = "được xác nhận bởi itcctv";
        $data['hotbutton_link']= "";
        $data['page_up_title'] ="Làm bài trắc nghiệm ".$hocphan->title  ;


        return view('Exercise::'.$this->frontend.'.luyentaptrachnghiem',$data);
        
    }
    public function viewketqua($bodeId)
    {
            // dd($request);
        $bode = Bodetracnghiem::findOrFail($bodeId);
        $questions = $bode->tracnghiemCauhois();
        $hocphan_id = $bode->hocphan_id;
        $hocphan =  Hocphan::find( $hocphan_id);
        $data['hocphan'] = $hocphan;
        $data['questions'] = $bode->tracnghiemCauhois();
        $data['bode'] = $bode;
        $totalPoints = 0; // Tổng điểm đạt được
        $correctAnswers = 0;
    
        // Duyệt qua từng câu hỏi và kiểm tra đáp án
        
        $enrollment = Enrollment::where('user_id',auth()->id())
            ->where('phancong_id',  $bode->hocphan_id)->first();
        $enrolltracnghiem = EnrollmentTracnghiem::where('user_id', auth()->id())
            ->where('enroll_id', $enrollment->id)
            ->where('bode_id', $bode->id)
            ->first();
        
        
        $data['enrolltracnghiem']  = $enrolltracnghiem ;
        $data['correctAnswers'] = $correctAnswers;
        $data['detail'] = \App\Models\SettingDetail::find(1);  
        $data['categories'] = \App\Models\Category::where('status','active')->where('parent_id',null)->get();
        $user  = auth()->user();
            ////
            
        $data['pagetitle']="Kết quả " ;
        $data['page_up_title'] = "Kết quả " ;
        $data['page_subtitle']="Kết quả " ;
        $data['page_title']= " " ;
        $data['hotbutton_title'] = "Doanh nghiệp gần bạn nhất"  ;
        $data['hotbutton_subtitle'] = "được xác nhận bởi itcctv";
        $data['hotbutton_link']= "";
        $data['page_up_title'] ="Kết quả " ;

        // kiem tra cap chung chi

        if($hocphan->tracnghiem_point <= $enrollment->tracnghiem_point && $hocphan->tuluan_point <= $enrollment->trachnghiem_point 
            && $hocphan->time_point <= $enrollment->time_point)
        {
            $certificate = Certificate::where('user_id', auth()->id())
            ->where('hocphan_id', $hocphan->id)
            ->first();
            if(!$certificate)
            {
                $certificate = Certificate::create([
                    'user_id' => auth()->id(),
                    'certificate_number' => 'CER'.uniqid(),
                    'certificate_name' => $hocphan->title,
                    'hocphan_id' => $hocphan->id,
                    'issued_date' => now(),
                ]);
            }
            
            
        }

        return view('Exercise::'.$this->frontend.'.tracnghiemresult',$data);
    }
    public function submit(Request $request, $bodeId)
    {
        // dd($request);
        $bode = Bodetracnghiem::findOrFail($bodeId);
        $questions = $bode->tracnghiemCauhois();
        $hocphan_id = $bode->hocphan_id;
        $hocphan =  Hocphan::find( $hocphan_id);
        $data['hocphan'] = $hocphan;
        $data['questions'] = $bode->tracnghiemCauhois();
        $data['bode'] = $bode;
        $totalPoints = 0; // Tổng điểm đạt được
        $correctAnswers = 0;
    
        // Duyệt qua từng câu hỏi và kiểm tra đáp án
        foreach ($questions as $question) {
            $selectedAnswerId = $request->input("answers.{$question->id}");
            $correctAnswer = $question->answers->where('is_correct', true)->first();
            $questionsWithPoints = [];
            if ($correctAnswer && $selectedAnswerId == $correctAnswer->id) {
                // Tăng điểm nếu trả lời đúng
                $point= collect($bode->questions)
                    ->where('id_question', $question->id)
                    ->first()['points'];
                $totalPoints += $point;
                $correctAnswers++;
                $questionsWithPoints[] = [
                    'id_question' => $question->id,
                    'points' =>$point, // Làm tròn đến 2 chữ số
                ];
            }
            else
            {
                $questionsWithPoints[] = [
                    'id_question' => $question->id,
                    'points' => 0, // Làm tròn đến 2 chữ số
                ];
            }
        }
        $enrollment = Enrollment::where('user_id',auth()->id())
            ->where('phancong_id',  $bode->hocphan_id)->first();
            $enrolltracnghiem = EnrollmentTracnghiem::where('user_id', auth()->id())
            ->where('enroll_id', $enrollment->id)
            ->where('bode_id', $bode->id)
            ->first();
        
        if (!$enrolltracnghiem) {
            // Nếu chưa tồn tại, thêm bản ghi mới
            $enrolltracnghiem = EnrollmentTracnghiem::create([
                'user_id' => auth()->id(),
                'enroll_id' => $enrollment->id,
                'bode_id' => $bode->id,
                'point' => $totalPoints,
                'time' => (int)($request->time_taken/60) + 1,
            ]);
            $enrollment->timespending += (int)($request->time_taken/60) + 1;
            $enrollment->tracnghiem_point += $totalPoints ;
            $enrolltracnghiem->questions = json_encode( $questionsWithPoints);
            $enrolltracnghiem->save();
            $enrollment->save();
           
        }
        $data['enrolltracnghiem']  = $enrolltracnghiem ;
        $data['correctAnswers'] = $correctAnswers;
        $data['detail'] = \App\Models\SettingDetail::find(1);  
        $data['categories'] = \App\Models\Category::where('status','active')->where('parent_id',null)->get();
        $user  = auth()->user();
         ////
         
        $data['pagetitle']="Kết quả " ;
        $data['page_up_title'] = "Kết quả " ;
        $data['page_subtitle']="Kết quả " ;
        $data['page_title']= " " ;
        $data['hotbutton_title'] = "Doanh nghiệp gần bạn nhất"  ;
        $data['hotbutton_subtitle'] = "được xác nhận bởi itcctv";
        $data['hotbutton_link']= "";
        $data['page_up_title'] ="Kết quả " ;

        // kiem tra cap chung chi

        if($hocphan->tracnghiem_point <= $enrollment->tracnghiem_point && $hocphan->tuluan_point <= $enrollment->trachnghiem_point 
            && $hocphan->time_point <= $enrollment->time_point)
            {
                $certificate = Certificate::where('user_id', auth()->id())
                ->where('hocphan_id', $hocphan->id)
                ->first();
                if(!$certificate)
                {
                    $certificate = Certificate::create([
                        'user_id' => auth()->id(),
                        'certificate_number' => 'CER'.uniqid(),
                        'certificate_name' => $hocphan->title,
                        'hocphan_id' => $hocphan->id,
                        'issued_date' => now(),
                    ]);
                }
               
               
            }
         // Tạo mới giấy chứng nhận
       
        

        // Chuyển hướng sau khi xử lý
        // return view('Exercise::'.$this->frontend.'.tracnghiemresult',$data);
        return redirect()->route('front.tracnghiem.result',  $bodeId);
    }
}