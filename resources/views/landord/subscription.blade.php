@extends('layouts.landord')

@section('content')
<div class="main">
    <div class="title2">CHOOSE YOUR PLAN</div>
    <div class="plans">
        <!-- Free Plan -->
        <div class="plan">
            <div class="plan-header free">
                <h2>FREE</h2>
            </div>
            <div class="plan-body">
                <ul>
                    <li><i class="fas fa-check text-green-500"></i>Your Text Here</li>
                    <li><i class="fas fa-check text-green-500"></i>Sample Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Sample Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Text Input</li>
                </ul>
                <div class="price free">$0.00</div>
                <div class="buy">
                    <button>BUY NOW</button>
                </div>
            </div>
        </div>
        <!-- Basic Plan -->
        <div class="plan">
            <div class="plan-header basic">
                <h2>BASIC</h2>
            </div>
            <div class="plan-body">
                <ul>
                    <li><i class="fas fa-check text-green-500"></i>Your Text Here</li>
                    <li><i class="fas fa-check text-green-500"></i>Sample Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Sample Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Text Input</li>
                </ul>
                <div class="price basic">$19.00</div>
                <div class="buy">
                    <button>BUY NOW</button>
                </div>
            </div>
        </div>
        <!-- Standard Plan -->
        <div class="plan">
            <div class="plan-header standard">
                <h2>STANDARD</h2>
            </div>
            <div class="plan-body">
                <ul>
                    <li><i class="fas fa-check text-green-500"></i>Your Text Here</li>
                    <li><i class="fas fa-check text-green-500"></i>Sample Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Sample Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Text Input</li>
                </ul>
                <div class="price standard">$39.00</div>
                <div class="buy">
                    <button>BUY NOW</button>
                </div>
            </div>
        </div>
        <!-- Premium Plan -->
        <div class="plan">
            <div class="plan-header premium">
                <h2>PREMIUM</h2>
            </div>
            <div class="plan-body">
                <ul>
                    <li><i class="fas fa-check text-green-500"></i>Your Text Here</li>
                    <li><i class="fas fa-check text-green-500"></i>Sample Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Sample Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Text Here</li>
                    <li><i class="fas fa-times text-red-500"></i>Your Text Input</li>
                </ul>
                <div class="price premium">$69.00</div>
                <div class="buy">
                    <button>BUY NOW</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection