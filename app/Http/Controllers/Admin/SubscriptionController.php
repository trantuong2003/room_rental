<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPackage;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = SubscriptionPackage::All();
        return view('admin.subscription', compact('subscriptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'post_limit' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        SubscriptionPackage::create($request->all());
        return redirect('/admin/subscription')->with('success', 'Subscription package created successfully!');
    }

    public function edit($id)
    {
        $subscription = SubscriptionPackage::find($id);

        if (!$subscription) {
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        return response()->json($subscription);
    }

    public function update(Request $request, $id)
    {
        $subscription = SubscriptionPackage::findOrFail($id);

        $request->validate([
            'package_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'post_limit' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $subscription->update($request->all());

        return redirect()->route('subscriptions.index')->with('success', 'Subscription package updated successfully!');
    }

    public function destroy($id)
    {
        $subscription = SubscriptionPackage::findOrFail($id);
        $subscription->delete();

        return redirect()->route('subscriptions.index')->with('success', 'Subscription deleted successfully!');
    }
}
