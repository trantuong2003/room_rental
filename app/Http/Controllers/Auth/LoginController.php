<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('account.login');
    }

    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $status = Auth::attempt(['email' => $email, 'password' => $password]);
        if ($status) {
            $user = Auth::user();
            // if (!$user->hasVerifiedEmail() && $user->role !== 'admin') {
            //     Auth::logout(); // Đăng xuất người dùng nếu chưa xác thực email
            //     return back()->with('msg', 'Vui lòng xác thực email trước khi đăng nhập.');
            // }
            // $urlRedirect = "/";
            if ($user->role === 'admin') {
                $urlRedirect = "/admin";
            } elseif ($user->role === 'landlord') {
                $urlRedirect = "/landlord"; // Thêm điều hướng cho landlord
            }elseif ($user->role === 'customer'){
                $urlRedirect = "/customer";
            }
            return redirect($urlRedirect);
        }
        return back()->with('msg', 'Email hoặc mật khẩu không chính xác');
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Đăng xuất người dùng

        $request->session()->invalidate(); // Hủy session
        $request->session()->regenerateToken(); // Tạo lại CSRF token

        return redirect('/login'); // Chuyển hướng về trang đăng nhập
    }
}
