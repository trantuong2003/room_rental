<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/subscription.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/moderation_post.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/banned_word.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/approval_customer_post.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/account.css') }}">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <script src="{{ asset('assets/js/admin.js') }}"></script>
</head>

<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-smile'></i>
            <span class="text">AdminHub</span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="/admin">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/admin/account/profile">
                    <i class='bx bxs-group'></i>
                    <span class="text">Account</span>
                </a>
            </li>
            <li>
                <a href="/admin/subscription">
                    <i class='bx bxs-shopping-bag-alt'></i>
                    <span class="text">Packages</span>
                </a>
            </li>
            <li>
                <a href="/admin/moderation_post">
                    <i class='bx bxs-doughnut-chart'></i>
                    <span class="text">Moderation Post</span>
                </a>
            </li>
            <li>
                <a href="/admin/moderation/customer/post">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Approval Blog</span>
                </a>
            </li>
            <li>
                <a href="/admin/banned-words">
                    <i class='bx bxs-message-dots'></i>
                    <span class="text">Comment</span>
                </a>
            </li>
            <li>
                <a href="/admin/transactions" >
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Transactions</span>
                </a>
            </li>


        </ul>
        {{-- <ul class="side-menu">

            <li>
                <a href="#" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul> --}}
    </section>
    <!-- SIDEBAR -->



    <!-- CONTENT -->
    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Categories</a>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <span class="num">8</span>
            </a>
            <a href="#" class="profile">
                <img src="assets/image/people.png">
            </a>
            <div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>

        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            @yield('content')
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

</body>

</html>