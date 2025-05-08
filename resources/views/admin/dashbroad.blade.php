@extends('layouts.admin')

@section('content')
    <div class="head-title">
        <div class="left">
            <h1>Dashboard</h1>
            <ul class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li><a class="active" href="#">Home</a></li>
            </ul>
        </div>
        {{-- <a href="#" class="btn-download">
            <i class='bx bxs-cloud-download'></i>
            <span class="text">Download PDF</span>
        </a> --}}
    </div>

    <!-- Summary Boxes -->
    <ul class="box-info">
        <li>
            <i class='bx bxs-group'></i>
            <span class="text">
                <h3>{{ $totalUsers }}</h3>
                <p>Total Users</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-home'></i>
            <span class="text">
                <h3>{{ $pendingLandlordPosts }}</h3>
                <p>Pending Landlord Posts</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-user'></i>
            <span class="text">
                <h3>{{ $pendingCustomerPosts }}</h3>
                <p>Pending Customer Posts</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-dollar-circle'></i>
            <span class="text">
                <h3>${{ number_format($totalRevenue, 2) }}</h3>
                <p>Total Revenue</p>
            </span>
        </li>
    </ul>

    <!-- Charts Section -->
    <div class="table-data">
        <!-- Monthly Revenue Chart -->
        <div class="order">
            <div class="head">
                <h3>Monthly Revenue ({{ now()->year }})</h3>
            </div>
            <canvas id="revenueChart" style="max-height: 300px;"></canvas>
        </div>
        <!-- Subscription Package Popularity Chart -->
        <div class="todo">
            <div class="head">
                <h3>Subscription Package Popularity</h3>
            </div>
            <canvas id="packagePopularityChart" style="max-height: 300px;"></canvas>
        </div>
        <!-- User Role Distribution Chart -->
        <div class="todo">
            <div class="head">
                <h3>User Role Distribution</h3>
            </div>
            <canvas id="userRoleChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Revenue Chart (Bar)
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [@foreach ($monthlyRevenue as $amount) {{ $amount }}, @endforeach],
                    backgroundColor: 'rgba(60, 145, 230, 0.6)',
                    borderColor: 'rgba(60, 145, 230, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });

        // Subscription Package Popularity Chart (Pie)
        const packagePopularityCtx = document.getElementById('packagePopularityChart').getContext('2d');
        const packagePopularityChart = new Chart(packagePopularityCtx, {
            type: 'pie',
            data: {
                labels: [@foreach ($subscriptionPackages as $package) '{{ $package['name'] }}', @endforeach],
                datasets: [{
                    data: [@foreach ($subscriptionPackages as $package) {{ $package['subscriptions_count'] }}, @endforeach],
                    backgroundColor: [
                        'rgba(60, 145, 230, 0.6)',
                        'rgba(255, 206, 38, 0.6)',
                        'rgba(253, 114, 56, 0.6)',
                        'rgba(219, 80, 74, 0.6)',
                        'rgba(102, 102, 102, 0.6)'
                    ],
                    borderColor: [
                        'rgba(60, 145, 230, 1)',
                        'rgba(255, 206, 38, 1)',
                        'rgba(253, 114, 56, 1)',
                        'rgba(219, 80, 74, 1)',
                        'rgba(102, 102, 102, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // User Role Distribution Chart (Pie)
        const userRoleCtx = document.getElementById('userRoleChart').getContext('2d');
        const userRoleChart = new Chart(userRoleCtx, {
            type: 'pie',
            data: {
                labels: ['Landlord', 'Customer'],
                datasets: [{
                    data: [{{ $userRoles['landlord'] }}, {{ $userRoles['customer'] }}],
                    backgroundColor: [
                        'rgba(255, 206, 38, 0.6)',
                        'rgba(253, 114, 56, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 206, 38, 1)',
                        'rgba(253, 114, 56, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection