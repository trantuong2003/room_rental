@extends('layouts.landord')

@section('content')
<div class="main">

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="title2">CHOOSE YOUR PLAN</div>
    <div class="plans">
        @if($subscriptionPackage->count() > 0)
        @foreach($subscriptionPackage as $package)
        <div class="plan">
            <div class="plan-header {{ strtolower($package->package_name) }}">
                <h2>{{ strtoupper($package->package_name) }}</h2>
            </div>
            <div class="plan-body">
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
                <div class="price standard {{ strtolower($package->package_name) }}">
                    ${{ number_format($package->price, 2) }}
                </div>
                <div class="buy">
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