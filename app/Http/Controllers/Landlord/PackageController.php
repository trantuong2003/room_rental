<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPackage;

class PackageController extends Controller
{
    public function index()
    {
        $subscriptionPackage = SubscriptionPackage::all(); 
        return view('landord.subscription', compact('subscriptionPackage'));
    }
}
