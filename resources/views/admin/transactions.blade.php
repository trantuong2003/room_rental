@extends('layouts.admin')

@section('content')
    <div class="head-title">
        <div class="left">
            <h1>Transaction History</h1>
            <ul class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li><a class="active" href="{{ route('admin.transactions') }}">Transactions</a></li>
            </ul>
        </div>
    </div>

    <!-- Search Form -->
    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Filter Transactions</h3>
            </div>
            <form action="{{ route('admin.transactions') }}" method="GET" class="form-input">
                <div style="display: flex; gap: 16px; align-items: center;">
                    <div>
                        <label for="start_date">Start Date:</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" style="padding: 8px; border-radius: 5px; border: 1px solid var(--grey);">
                    </div>
                    <div>
                        <label for="end_date">End Date:</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" style="padding: 8px; border-radius: 5px; border: 1px solid var(--grey);">
                    </div>
                    <button type="submit" style="padding: 8px 16px; background: var(--blue); color: var(--light); border: none; border-radius: 5px; cursor: pointer;">
                        <i class='bx bx-search'></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Recent Transactions</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>User</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->txn_ref }}</td>
                            <td>{{ $transaction->user->name }}</td>
                            <td>{{ $transaction->package->package_name }}</td>
                            <td>${{ number_format($transaction->amount, 2) }}</td>
                            <td>{{ ucfirst($transaction->payment_method) }}</td>
                            <td>
                                <span class="status {{ $transaction->status }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Pagination Links -->
            <div style="margin-top: 20px;">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Additional CSS for Status Styling -->
    <style>
        .status {
            font-size: 10px;
            padding: 6px 16px;
            color: var(--light);
            border-radius: 20px;
            font-weight: 700;
            display: inline-block;
        }
        .status.completed {
            background: var(--blue);
        }
        .status.pending {
            background: var(--orange);
        }
        .status.failed {
            background: var(--red);
        }
    </style>
@endsection