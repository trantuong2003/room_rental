<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Landlord Dashboard</title>
    <link rel="stylesheet" href="{{ asset('assets/css/landlord/landord.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/landlord/payment_history.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/landlord/create_posts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/landlord/post.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/landlord/detail_post.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/landlord/message.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/landlord/subscription.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/landlord/edit_post.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>

<body>
    <!-- Mobile toggle button that's always visible on mobile -->
    <div class="mobile-toggle">
        <ion-icon name="menu-outline"></ion-icon>
    </div>
    
    <!-- Mobile overlay for closing the menu -->
    <div class="mobile-overlay"></div>
    
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="home"></ion-icon>
                        </span>
                        <span class="title">Rental Dashboard</span>
                    </a>
                </li>

                <li id="dashboard-link">
                    <a href="/landlord">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li id="posts-link">
                    <a href="/landlord/posts">
                        <span class="icon">
                            <ion-icon name="document-text-outline"></ion-icon>
                        </span>
                        <span class="title">Posts</span>
                    </a>
                </li>

                <li id="chat-link">
                    <a href="/landlord/chat">
                        <span class="icon">
                            <ion-icon name="chatbubble-outline"></ion-icon>
                        </span>
                        <span class="title">Messages</span>
                    </a>
                </li>

                <li id="subscription-link">
                    <a href="/landlord/subscription">
                        <span class="icon">
                            <ion-icon name="card-outline"></ion-icon>
                        </span>
                        <span class="title">Subscriptions</span>
                    </a>
                </li>

                <li id="history-link">
                    <a href="/landlord/history">
                        <span class="icon">
                            <ion-icon name="receipt-outline"></ion-icon>
                        </span>
                        <span class="title">Payment History</span>
                    </a>
                </li>

                <li>
                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-button">
                            <span class="icon">
                                <ion-icon name="log-out-outline"></ion-icon>
                            </span>
                            <span class="title">Sign Out</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
        <main class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <div class="search">
                    <label>
                        <input type="text" placeholder="Search here">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>
                
                <div class="user-profile">
                    <div class="user">
                        <img src="{{ asset('assets/image/default-avatar.jpg') }}" alt="User Avatar">
                    </div>
                </div>
            </div>
            
            <div class="container_content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <!-- =========== Scripts =========  -->
    <script>
        // Get all elements
        let toggle = document.querySelector('.toggle');
        let mobileToggle = document.querySelector('.mobile-toggle');
        let navigation = document.querySelector('.navigation');
        let main = document.querySelector('.main');
        let container = document.querySelector('.container');
        let mobileOverlay = document.querySelector('.mobile-overlay');
        
        // Function to toggle menu state
        function toggleMenu() {
            navigation.classList.toggle('active');
            main.classList.toggle('active');
            container.classList.toggle('active');
            mobileOverlay.classList.toggle('active');
            
            // Update toggle icons
            if (navigation.classList.contains('active')) {
                toggle.innerHTML = '<ion-icon name="close-outline"></ion-icon>';
                mobileToggle.innerHTML = '<ion-icon name="close-outline"></ion-icon>';
            } else {
                toggle.innerHTML = '<ion-icon name="menu-outline"></ion-icon>';
                mobileToggle.innerHTML = '<ion-icon name="menu-outline"></ion-icon>';
            }
        }
        
        // Desktop toggle
        toggle.addEventListener('click', toggleMenu);
        
        // Mobile toggle
        mobileToggle.addEventListener('click', toggleMenu);
        
        // Close menu when clicking on the overlay
        mobileOverlay.addEventListener('click', function() {
            if (navigation.classList.contains('active')) {
                toggleMenu();
            }
        });
        
        // Set active menu item based on current URL
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            
            // Define the mapping of paths to menu items
            const menuItems = {
                '/landlord': 'dashboard-link',
                '/landlord/posts': 'posts-link',
                '/landlord/chat': 'chat-link',
                '/landlord/subscription': 'subscription-link',
                '/landlord/history': 'history-link'
            };
            
            // Check for exact matches first
            if (menuItems[currentPath]) {
                document.getElementById(menuItems[currentPath]).classList.add('active');
            } else {
                // Check for partial matches (for subpages)
                for (const path in menuItems) {
                    if (currentPath.startsWith(path) && path !== '/landlord') {
                        document.getElementById(menuItems[path]).classList.add('active');
                        break;
                    }
                }
                
                // Special case for dashboard (to avoid matching all /landlord paths)
                if (currentPath === '/landlord' || currentPath === '/landlord/') {
                    document.getElementById('dashboard-link').classList.add('active');
                }
            }
        });
        
        // Add hover effect (but don't remove active class)
        let list = document.querySelectorAll('.navigation li');
        function hoverEffect() {
            list.forEach((item) => {
                if (!item.classList.contains('active')) {
                    item.classList.remove('hovered');
                }
            });
            
            if (!this.classList.contains('active')) {
                this.classList.add('hovered');
            }
        }
        
        list.forEach((item) => {
            item.addEventListener('mouseenter', hoverEffect);
            item.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.classList.remove('hovered');
                }
            });
        });
    </script>
</body>
</html>
