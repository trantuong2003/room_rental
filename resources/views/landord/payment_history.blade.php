@extends('layouts.landord')

@section('content')
<div class="payment-history">
    <div class="container">
        <h1>
            <ion-icon name="receipt-outline"></ion-icon>
            Payment History
        </h1>
        
        <div class="filters">
            <div class="date-range">
                <label>
                    <ion-icon name="calendar-outline"></ion-icon>
                    From:
                </label>
                <input type="date" id="start-date" name="start_date">
                
                <label>
                    <ion-icon name="calendar-outline"></ion-icon>
                    To:
                </label>
                <input type="date" id="end-date" name="end_date">
            </div>
            
            <div class="search-box">
                <ion-icon name="search-outline"></ion-icon>
                <input type="text" placeholder="Search by payment code..." id="search-input">
            </div>
            
            <div class="buttons">
                <button class="filter-button" id="filter-button">
                    <ion-icon name="funnel-outline"></ion-icon>
                    Filter
                </button>
                
                <button class="reset-button" id="reset-button">
                    <ion-icon name="refresh-outline"></ion-icon>
                    Reset
                </button>
            </div>
        </div>

        <div></div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Transaction Time</th>
                        <th>Payment Method</th>
                        <th>Payment Code</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($history) > 0)
                        @foreach($history as $payment)
                        <tr>
                            <td>{{ $payment->created_at }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>{{ $payment->txn_ref }}</td>
                            <td>{{ number_format($payment->amount, 0) }} VND</td>
                            <td>
                                @php
                                    $statusClass = 'pending';
                                    if(strtolower($payment->status) == 'success' || strtolower($payment->status) == 'completed') {
                                        $statusClass = 'success';
                                    } elseif(strtolower($payment->status) == 'failed' || strtolower($payment->status) == 'cancelled') {
                                        $statusClass = 'failed';
                                    } elseif(strtolower($payment->status) == 'processing') {
                                        $statusClass = 'processing';
                                    }
                                @endphp
                                <span class="status {{ $statusClass }}">{{ $payment->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <ion-icon name="receipt-outline"></ion-icon>
                                    <p>No payment history found</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Add pagination if needed -->
        @if(isset($history) && method_exists($history, 'links') && $history->lastPage() > 1)
        <div class="pagination">
            {{ $history->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default date values (last 30 days)
        const today = new Date();
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(today.getDate() - 30);
        
        document.getElementById('end-date').valueAsDate = today;
        document.getElementById('start-date').valueAsDate = thirtyDaysAgo;
        
        // Filter functionality
        const filterButton = document.getElementById('filter-button');
        const resetButton = document.getElementById('reset-button');
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const searchInput = document.getElementById('search-input');
        const tableRows = document.querySelectorAll('.payment-history tbody tr');
        
        filterButton.addEventListener('click', function() {
            const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
            const endDate = endDateInput.value ? new Date(endDateInput.value) : null;
            const searchTerm = searchInput.value.toLowerCase();
            
            tableRows.forEach(row => {
                if (row.querySelector('.empty-state')) return; // Skip empty state row
                
                const dateCell = row.cells[0].textContent;
                const paymentCode = row.cells[2].textContent.toLowerCase();
                const rowDate = new Date(dateCell);
                
                let showRow = true;
                
                // Filter by date range
                if (startDate && endDate) {
                    // Set end date to end of day for inclusive comparison
                    const endOfDay = new Date(endDate);
                    endOfDay.setHours(23, 59, 59, 999);
                    
                    if (rowDate < startDate || rowDate > endOfDay) {
                        showRow = false;
                    }
                }
                
                // Filter by search term
                if (searchTerm && !paymentCode.includes(searchTerm)) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
            
            // Show empty state if no results
            const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
            if (visibleRows.length === 0 && tableRows.length > 0) {
                // If there's no empty state row yet, add one
                if (!document.querySelector('.empty-state')) {
                    const tbody = document.querySelector('.payment-history tbody');
                    const emptyRow = document.createElement('tr');
                    emptyRow.classList.add('empty-row');
                    emptyRow.innerHTML = `
                        <td colspan="5">
                            <div class="empty-state">
                                <ion-icon name="search-outline"></ion-icon>
                                <p>No results found for your search</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                } else {
                    // Show existing empty state row
                    document.querySelector('.empty-row').style.display = '';
                }
            } else {
                // Hide empty state row if it exists
                const emptyRow = document.querySelector('.empty-row');
                if (emptyRow) {
                    emptyRow.style.display = 'none';
                }
            }
        });
        
        resetButton.addEventListener('click', function() {
            // Reset date inputs to last 30 days
            document.getElementById('end-date').valueAsDate = today;
            document.getElementById('start-date').valueAsDate = thirtyDaysAgo;
            
            // Clear search input
            searchInput.value = '';
            
            // Show all rows
            tableRows.forEach(row => {
                row.style.display = '';
            });
            
            // Hide empty row if it exists
            const emptyRow = document.querySelector('.empty-row');
            if (emptyRow) {
                emptyRow.style.display = 'none';
            }
        });
    });
</script>
@endsection
