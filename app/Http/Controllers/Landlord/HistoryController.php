<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class HistoryController extends Controller
{
    public function ShowHistory()
    {
        $history = Payment::all(); 
        return view('landord.payment_history', compact('history'));
    }
}
