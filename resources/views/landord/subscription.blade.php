@extends('layouts.landord')

@section('content')
<div class="main subscription_page">

    @if(session('success'))
    <div class="subscription_alert subscription_alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="subscription_alert subscription_alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="subscription_title">CHOOSE YOUR PLAN</div>
    <div class="subscription_plans">
        @if($subscriptionPackage->count() > 0)
        @foreach($subscriptionPackage as $package)
        <div class="subscription_plan">
            <div class="subscription_plan-header {{ strtolower($package->package_name) }}">
                <h2>{{ strtoupper($package->package_name) }}</h2>
            </div>
            <div class="subscription_plan-body">
                <ul>
                    @php
                    $features = explode('.', $package->description);
                    @endphp
                    @foreach($features as $feature)
                    @if(!empty(trim($feature)))
                    <li><i class="fas fa-check text-green-500"></i> {{ trim($feature) }}</li>
                    @endif
                    @endforeach
                </ul>
                <div class="subscription_price {{ strtolower($package->package_name) }}">
                    ${{ number_format($package->price, 2) }}
                </div>
                <div class="subscription_buy">
                    <form action="{{ url('/landlord/vnpaypayment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="price" value="{{ $package->price }}">
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <button onclick="subscribe({{ $package->id }})">BUY NOW</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        @else
        <p>No subscriptions available.</p>
        @endif
    </div>
</div>
@endsection
