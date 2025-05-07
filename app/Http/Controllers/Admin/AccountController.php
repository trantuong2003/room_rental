<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AccountController extends Controller
{
    public function profile(Request $request)
    {
        // Lấy loại người dùng cần lọc
        $filter = $request->query('filter', 'all');

        // Đếm số lượng
        $countAll = User::whereIn('role', ['customer', 'landlord'])->count();
        $countLandlord = User::where('role', 'landlord')->count();
        $countCustomer = User::where('role', 'customer')->count();

        // Lấy danh sách người dùng
        if ($filter == 'landlord') {
            $users = User::where('role', 'landlord')->get();
        } elseif ($filter == 'customer') {
            $users = User::where('role', 'customer')->get();
        } else {
            $users = User::whereIn('role', ['customer', 'landlord'])->get();
        }

        return view('admin.account', compact('users', 'filter', 'countAll', 'countLandlord', 'countCustomer'));
    }
}
