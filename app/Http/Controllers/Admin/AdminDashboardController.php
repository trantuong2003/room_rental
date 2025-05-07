<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LandlordPost;
use App\Models\CustomerPost;
use App\Models\Payment;
use App\Models\SubscriptionPackage;
class AdminDashboardController extends Controller
{
    public function index()
    {
        // Total users (excluding admins)
        $totalUsers = User::where('role', '!=', 'admin')->count();

        // Pending landlord posts
        $pendingLandlordPosts = LandlordPost::where('status', 'pending')->count();

        // Pending customer posts
        $pendingCustomerPosts = CustomerPost::where('status', 'pending')->count();

        // Total revenue (all-time)
        $totalRevenue = Payment::sum('amount');

        // Monthly revenue for the current year
        $monthlyRevenue = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyRevenue[$month] = Payment::whereMonth('created_at', $month)
                ->whereYear('created_at', now()->year)
                ->sum('amount');
        }

        // Subscription package popularity
        $subscriptionPackages = SubscriptionPackage::withCount('subscriptions')->get()->map(function ($package) {
            return [
                'name' => $package->package_name,
                'subscriptions_count' => $package->subscriptions_count
            ];
        })->toArray();

        // User role distribution (excluding admins)
        $userRoles = [
            'landlord' => User::where('role', 'landlord')->count(),
            'customer' => User::where('role', 'customer')->count()
        ];

        return view('admin.dashbroad', compact(
            'totalUsers',
            'pendingLandlordPosts',
            'pendingCustomerPosts',
            'totalRevenue',
            'monthlyRevenue',
            'subscriptionPackages',
            'userRoles'
        ));
    }

    public function transactionHistory(Request $request)
    {
        $query = Payment::with(['user', 'package'])
            ->orderBy('created_at', 'desc');

        // Apply date range filter if provided
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        // Paginate results (10 per page)
        $transactions = $query->paginate(10);

        return view('admin.transactions', compact('transactions'));
    }
}
