<?php
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

 $imageData = file_get_contents('https://itcctv.vn/storage/photos/1/ce3012ad26a4b.webp');
// Mã hóa ảnh thành Base64
$base64Image = base64_encode($imageData);
// Tạo chuỗi Base64 hoàn chỉnh
$base64ImageSrc = 'data:image/png;base64,' . $base64Image;
 
// Mã hóa ảnh thành Base64
 $link = route('front.hocphan.certificate',$certificate->id);
// Tạo đối tượng QrCode
$qrCode = new QrCode($link) ;
       

// Cấu hình QR Code
// $qrCode->setEncoding(new Encoding('UTF-8'))
//     ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
//     ->setSize(300)
//     ->setMargin(10);

// Sử dụng PngWriter để xuất QR Code
$writer = new PngWriter();
$result = $writer->write($qrCode);
// Chuyển QR Code thành Base64 để nhúng vào HTML
$qrBase64 = 'data:image/png;base64,' . base64_encode($result->getString());


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giấy Chứng Nhận</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
         
            padding: 50px;
        }
        html, body {
            background-color: white;
        }

        .certificate {
            position: relative;
            border: 5px solid #000;
            background-color: #fff;
            height: 80%;
            width: 80%;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
           
        }
        .certificate::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-image: url('{{$base64ImageSrc}}'); /* Đường dẫn logo */
            background-size: 50%; /* Kích thước logo */
            background-repeat: no-repeat;
            opacity: 0.1; /* Độ mờ của logo */
            width: 100%;
            height: 100%;
            z-index: 0; /* Đặt phía sau nội dung */
        }
        h1, h2, h3, p {
            position: relative; /* Đặt phía trên logo */
            z-index: 1;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2rem;
            margin: 10px 0;
        }
        
  
             
    </style>
</head>

<body>
    <div style="width:100%; height:500px" >
        <div id="certificate">
            <div class="certificate"  >
                <br/><br/>
                <h1>Giấy Chứng Nhận</h1>
                <p>Chứng nhận rằng</p>
                <h2 id="user-name">{{$user->full_name}}</h2>
                <p>Đã hoàn thành khóa học:</p>
                <h3 id="course-name">{{$certificate->certificate_name}}</h3>
                <p>Ngày cấp: <span id="issued-date">{{$certificate->created_at}}</span></p>
                <p>Mã giấy chứng nhận: <span id="certificate-number">{{$certificate->certificate_number}}</span></p>
            
                <!-- QR Code -->
                <img style="width:100px; position:absolute; top: 10px; right:10px" class="qr-code" src="<?php echo $qrBase64; ?>" alt="QR Code"/>
                <br/><br/>
            </div>
            <br/>
            <br/>
            <br/>
            <br/>
        </div>
        <button id="download-button" style="margin-top: 20px; padding: 10px 20px; font-size: 1rem;">Tải xuống PDF</button>
        <button id="save">Chia sẻ</button>
    </div>
    
   
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        document.getElementById('save').addEventListener('click', function () {
            const content = document.getElementById('certificate');

            html2canvas(content).then(canvas => {
                const imageData = canvas.toDataURL('image/jpg'); // Chuyển canvas thành base64
                const formData = new FormData();
                formData.append('image', imageData);

                // Gửi ảnh đến server qua AJAX
                fetch('/share-certificate', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success)
                        window.location = data.url;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Chức năng tải xuống PDF
        document.getElementById("download-button").addEventListener("click", () => {
            const element = document.getElementById("certificate");
            const options = {
                margin: 10,
                filename: `certificate.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            // Tạo PDF và tải xuống
            html2pdf().set(options).from(element).save();
        });
    </script>
</body>
</html>
