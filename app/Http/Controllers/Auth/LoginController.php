<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function viewLogin()
    {
        return view('backend.auths.login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('admin/login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Kiểm tra nếu người dùng đã đăng nhập
        if (Auth::check()) {
            return redirect()->route('admin.home');
        }

        $olduser = \App\Models\User::where('phone', $request->email)
            ->orWhere('email', $request->email)
            ->first();

        if ($olduser) {
            if (Auth::attempt(['email' => $olduser->email, 'password' => $request->password])) {
                // Kiểm tra lại xem người dùng đã đăng nhập
                if (Auth::check()) {
                    if (isset($request->plink) && $request->plink != '') {
                        return redirect($request->plink);
                    } else {
                        return redirect()->route('admin.home');
                    }
                }
            } else {
                return redirect()->route('admin.login')
                    ->with('error', 'Email hoặc số điện thoại hoặc mật khẩu không đúng..');
            }
        } else {
            return redirect()->route('admin.login')
                ->with('error', 'Email hoặc số điện thoại hoặc mật khẩu không đúng.');
        }
    }

    public function credentials(Request $request)
    {
        return [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'active'
        ];
    }

    public function viewAdminlogin()
    {
        return view('auth.admin.login');
    }
}
