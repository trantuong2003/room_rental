<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('account.register');
    }

    public function register(Request $request)
    {
        try {
            // Validate dữ liệu
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'required|string|max:20',
                'role' => 'required|in:renter,landlord',
                'terms' => 'required|accepted',
            ]);

            // Xử lý dữ liệu tùy theo role
            if ($request->role === 'renter') {
                $request->validate([
                    'city' => 'required|string|max:255',
                    'region' => 'required|string|max:255',
                ]);
            } elseif ($request->role === 'landlord') {
                $request->validate([
                    'government_id' => 'required|string|max:255',
                    'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                ]);
            }

            // Lưu file proof (nếu có)
            $proofPath = null;
            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('proofs', 'public');
            }

            // Tạo người dùng mới
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => $request->role === 'renter' ? 'customer' : 'landlord',
                'address' => $request->role === 'renter' ? "{$request->city}, {$request->region}" : null,
                'government_id' => $request->role === 'landlord' ? $request->government_id : null,
                'proof' => $proofPath,
            ]);

            // Kích hoạt sự kiện Registered để gửi email xác nhận (không chặn phản hồi)
            event(new Registered($user));

            // Đăng nhập người dùng ngay sau khi đăng ký
            Auth::login($user);

            // Phản hồi nhanh chóng
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'success', 'Register success! Please check email.',
                    'redirect' => '/email/verify',
                ]);
            }

            // Chuyển hướng về trang xác minh email
            return redirect('/email/verify')->with('success', 'Register success! Please check email.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi đăng ký người dùng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đăng ký thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}