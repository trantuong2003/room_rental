@extends('layouts.landord')

@section('content')
<div class="main">
    <div class="payment-history">
        <div class="container">
            <h1>Lịch sử Thanh toán</h1>
            <div class="filters">
                <div>
                    <input type="date">

                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Thời gian giao dịch</th>
                            <th>Phương thức giao dịch</th>
                            <th>Mã thanh toán</th>
                            <th>Số tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $payment)
                        <tr>
                            <td>{{ $payment->created_at }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>{{ $payment->txn_ref }}</td>
                            <td>{{ $payment->amount }} VND</td>
                            <td>{{ $payment->status }}</td> {{-- Nếu có cột số dư hiện tại, bạn cần thêm logic để tính
                            --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection