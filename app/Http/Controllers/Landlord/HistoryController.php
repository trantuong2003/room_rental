<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function ShowHistory()
    {
        $userId = Auth::id(); // Lấy ID của người dùng hiện tại
        $history = Payment::where('user_id', $userId)->get();
        return view('landord.payment_history', compact('history'));
    }
}
