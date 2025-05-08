@extends('layouts.customer')

@section('content')
<div class="container">
    
    <!-- Search Bar -->
    <form id="search-form" method="GET" action="{{ route('customer.home') }}">
        <div class="search-bar">
            {{-- <i class="fas fa-search"></i> --}}
            <input type="text" name="search" placeholder="Search by location or post title" value="{{ request('search') }}">
            <button type="submit" class="search-button">Search</button>
            <button type="button" class="map-button"><i class="fas fa-map-marked-alt"></i> View Map</button>
        </div>
    </form>

    <!-- Filter Section -->
    <form id="filter-form" method="GET" action="{{ route('customer.home') }}">
        <div class="filter">

            <div class="filter-item">
                <div>
                    <span class="filter-name">Area</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="option">
                    <select name="area" onchange="this.form.submit()">
                        <option value="">Nationwide</option>
                        @php
                            $cities = [
                                'An Giang', 'Bà Rịa - Vũng Tàu', 'Bắc Giang', 'Bắc Kạn', 'Bạc Liêu', 'Bắc Ninh', 
                                'Bến Tre', 'Bình Định', 'Bình Dương', 'Bình Phước', 'Bình Thuận', 'Cà Mau', 
                                'Cần Thơ', 'Cao Bằng', 'Đà Nẵng', 'Đắk Lắk', 'Đắk Nông', 'Điện Biên', 'Đồng Nai', 
                                'Đồng Tháp', 'Gia Lai', 'Hà Giang', 'Hà Nam', 'Hà Nội', 'Hà Tĩnh', 'Hải Dương', 
                                'Hải Phòng', 'Hậu Giang', 'Hòa Bình', 'Hưng Yên', 'Khánh Hòa', 'Kiên Giang', 
                                'Kon Tum', 'Lai Châu', 'Lâm Đồng', 'Lạng Sơn', 'Lào Cai', 'Long An', 'Nam Định', 
                                'Nghệ An', 'Ninh Bình', 'Ninh Thuận', 'Phú Thọ', 'Phú Yên', 'Quảng Bình', 
                                'Quảng Nam', 'Quảng Ngãi', 'Quảng Ninh', 'Quảng Trị', 'Sóc Trăng', 'Sơn La', 
                                'Tây Ninh', 'Thái Bình', 'Thái Nguyên', 'Thanh Hóa', 'Thừa Thiên Huế', 'Tiền Giang', 
                                'Hồ Chí Minh', 'Trà Vinh', 'Tuyên Quang', 'Vĩnh Long', 'Vĩnh Phúc', 'Yên Bái'
                            ];
                        @endphp
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('area') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="filter-item">
                <div>
                    <span class="filter-name">Price Range</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="option">
                    <select name="price_range" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="0-2" {{ request('price_range') == '0-2' ? 'selected' : '' }}>Under 2 million</option>
                        <option value="2-3" {{ request('price_range') == '2-3' ? 'selected' : '' }}>2 - 3 million</option>
                        <option value="3-4" {{ request('price_range') == '3-4' ? 'selected' : '' }}>3 - 4 million</option>
                        <option value="4-999999" {{ request('price_range') == '4-999999' ? 'selected' : '' }}>Over 4 million</option>
                    </select>
                </div>
            </div>
            <div class="filter-item">
                <div>
                    <span class="filter-name">Area Size</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="option">
                    <select name="acreage_range" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="0-20" {{ request('acreage_range') == '0-20' ? 'selected' : '' }}>Under 20 m²</option>
                        <option value="20-30" {{ request('acreage_range') == '20-30' ? 'selected' : '' }}>20 - 30 m²</option>
                        <option value="30-40" {{ request('acreage_range') == '30-40' ? 'selected' : '' }}>30 - 40 m²</option>
                        <option value="40-999999" {{ request('acreage_range') == '40-999999' ? 'selected' : '' }}>Over 40 m²</option>
                    </select>
                </div>
            </div>
            <button type="button" class="reset-button" onclick="window.location.href='{{ route('customer.home') }}'">
                <i class="fas fa-sync-alt"></i>
                <span>Reset</span>
            </button>
        </div>
    </form>

    <!-- Listing -->
    @forelse($posts as $post)
    <div class="listing" onclick="redirectToDetail('{{ $post->type }}', {{ $post->id }})" style="cursor: pointer;">
        <div class="images">
            <img src="{{ asset('storage/' . $post->images->first()->image_path) }}" alt="Main image">
            <div class="grid">
                @foreach ($post->images->slice(1, 3) as $image)
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Additional image">
                @endforeach
            </div>
        </div>
        <div class="details">
            <div class="header">
                <h2>{{ $post->title }}</h2>
            </div>
            <div class="price">
                {{ $post->price }}
                <span>. {{ $post->acreage }}</span>
            </div>
            <div class="location">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $post->address }}</span>
            </div>
            <p>{{ Str::limit($post->description, 150) }}</p>
            <div class="footer">
                <div class="profile">
                    <img src="{{ asset('assets/image/userimage.jpg') }}" alt="Profile picture">
                    <div>
                        <p>{{ $post->user->name ?? 'Landlord' }}</p>
                    </div>
                </div>
                <div class="actions">
                    <button onclick="event.stopPropagation();">
                        <i class="fas fa-phone-alt"></i> {{ $post->user->phone ?? 'No phone number' }}
                    </button>
                    <button class="favorite-btn" 
                            data-post-id="{{ $post->id }}" 
                            data-post-type="{{ $post->type }}" 
                            style="cursor: pointer;"
                            onclick="event.stopPropagation(); handleFavoriteClick(event)">
                        <i class="fas fa-heart" style="color: {{ $post->isFavorited ? 'red' : 'gray' }};"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <p>No posts found matching the criteria.</p>
    @endforelse
</div>

<meta name="login-url" content="{{ route('login') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function redirectToDetail(postType, postId) {
        const route = postType === 'landlord' 
            ? "{{ route('customer.post.detail', '') }}/" + postId
            : "{{ route('customer.post.detail', '') }}/" + postId;
        window.location.href = route;
    }

    function handleFavoriteClick(event) {
        @if (Auth::check())
            toggleFavorite(event);
        @else
            Swal.fire({
                title: 'Login Required',
                text: 'Please log in to add to your favorites list!',
                icon: 'warning',
                confirmButtonText: 'Log In'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = document.querySelector('meta[name="login-url"]').getAttribute('content');
                }
            });
        @endif
    }

    async function toggleFavorite(event) {
        const button = event.currentTarget;
        const postId = button.getAttribute('data-post-id');
        const postType = button.getAttribute('data-post-type');
        const icon = button.querySelector('i');

        try {
            const response = await fetch("{{ route('customer.post.toggleFavorite') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ 
                    post_id: postId,
                    post_type: postType
                })
            });

            const data = await response.json();
            
            if (data.status === "added") {
                icon.style.color = "red";
            } else if (data.status === "removed") {
                icon.style.color = "gray";
            }
        } catch (error) {
            console.error("Error:", error);
            Swal.fire({
                title: 'Error',
                text: 'You need to log in to use this feature',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
</script>

@endsection