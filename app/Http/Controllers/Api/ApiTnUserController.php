<?php

namespace App\Http\Controllers\Api;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiTnUserController extends Controller
{
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            // successfull authentication
            $user = User::find(Auth::user()->id);
            if($user->status=='inactive')
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to authenticate.',
                ], 401);
            }
            else
            {
                $user_token['token'] = $user->createToken('appToken')->accessToken;

                return response()->json([
                    'success' => true,
                    'token' => $user_token,
                    'user' => $user,
                ], 200);
            }
            
        } else {
            // failure to authenticate
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }

    public function register(Request $request)
    {
        $this->validate($request,[
            'full_name'=>'string|required',
            'phone'=>'string|required',
            'email'=>'string|required',
            'password'=>'string|required',
        ]);
        $data = $request->all();

        $olduser =\App\Models\User::where('phone',$data['phone'])->get();
        if(count($olduser) > 0)
            return response()->json([
                'success' => false,
                'message' => 'Số điện thoại đã tồn tại',
            ], 201);
            
        $olduser = \App\Models\User::where('email',$data['email'])->get();
        if(count($olduser) > 0)
            return response()->json([
                'success' => false,
                'message' => 'Email đã tồn tại',
            ], 201); // Trả về 400 Bad Request cho lỗi

        $data['password'] = Hash::make($data['password']);
        // $data['username'] = $data['phone'];
        // $data['role'] = 'customer';
        $status = \App\Models\User::c_create($data);

        if(!$status) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xảy ra',
            ], 200);
        }    

        return response()->json([
            'full_name' => $data['full_name'],
            'password' => $data['password'],
            'phone' => $data['phone'],
            'email' => $data['email'],
        ], 200);
    }

    public function updateProfile(Request $request, $id)
    {
        // Xác thực dữ liệu đầu vào
        $this->validate($request, [
            'full_name' => 'string|nullable',
            'phone' => 'string|nullable',
            'email' => 'string|nullable|email',
        ]);

        // Tìm người dùng theo ID
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không tồn tại.',
            ], 404);
        }

        // Cập nhật thông tin người dùng nếu có
        if ($request->has('full_name')) {
            $user->full_name = $request->full_name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('email')) {
            // Kiểm tra xem email đã tồn tại chưa
            $existingEmailUser = User::where('email', $request->email)->first();
            if ($existingEmailUser && $existingEmailUser->id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email đã tồn tại',
                ], 400);
            }
            $user->email = $request->email;
        }

        // Lưu thông tin đã cập nhật
        if ($user->save()) {
            return response()->json([
                'success' => true,
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xảy ra khi cập nhật thông tin.',
            ], 500);
        }
    }
     
}