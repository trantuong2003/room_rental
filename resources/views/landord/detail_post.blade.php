@extends('layouts.landord')

@section('content')
<div class="main">
    <div>
        <div class="detail_container">
            <div class="card layout">
                <div class="left-section">
                    <!-- Display status notification if rejected -->
                    @if ($post->status === 'rejected')
                    <div class="alert alert-danger rejection-notice">
                        <h3><i class="fas fa-exclamation-circle"></i> Post has been rejected</h3>
                        <p><strong>Reason:</strong> {{ $post->rejection_reason ?: 'No specific reason provided' }}</p>
                    </div>
                    @endif

                    <!-- Display main image -->
                    <div class="image-container">
                        <!-- Main image -->
                        <div class="main-image">
                            @if ($post->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $post->images->first()->image_path) }}" alt="Room for rent">
                            @else
                            <img src="https://placehold.co/300x200" alt="No image">
                            @endif
                        </div>

                        <!-- Thumbnails -->
                        <div class="thumbnails">
                            @foreach ($post->images->slice(1, 6) as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                            @endforeach
                            @if ($post->images->count() > 7)
                            <div class="see-more-overlay" onclick="showAllImages({{ $post->id }})">
                                <span>+{{ $post->images->count() - 4 }}</span>
                                <p>View more</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- Modal to display all thumbnails -->
                    <div id="image-modal-{{ $post->id }}" class="image-modal hidden">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal({{ $post->id }})">×</span>
                            <div class="carousel">
                                @foreach ($post->images as $index => $image)
                                <div class="carousel-item"
                                    style="{{ $index === 0 ? 'display: block;' : 'display: none;' }}">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="Image {{ $index + 1 }}">
                                </div>
                                @endforeach
                                <button class="carousel-control prev"
                                    onclick="prevImage({{ $post->id }})">❮</button>
                                <button class="carousel-control next"
                                    onclick="nextImage({{ $post->id }})">❯</button>
                            </div>
                        </div>
                    </div>
                    <!-- Display post information -->
                    <h1 class="header">{{ $post->title }}</h1>
                    <p class="sub-header">{{ $post->address }}</p>
                    <hr>
                    <div class="info">
                        <div>
                            <p class="label">Price</p>
                            <p class="value">{{ $post->price }} </p>
                        </div>
                        <div>
                            <p class="label">Area</p>
                            <p class="value">{{ $post->acreage }} </p>
                        </div>
                        <div>
                            <p class="label">Bedrooms</p>
                            <p class="value">{{ $post->bedrooms }} </p>
                        </div>
                        <div class="icons">
                            <div>
                                <p class="label">Status</p>
                                <p class="value">{{ $post->status }}</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h2 class="section-title">Description</h2>
                    <p class="description">{{ $post->description }}</p>
                    <h2 class="section-title">Property Features</h2>
                    <hr>
                    <div class="features">
                        <div class="feature">
                            <i class="fas fa-money-bill-wave"></i>
                            <p>Price: {{ $post->price }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-ruler-combined"></i>
                            <p>Area: {{ $post->acreage }} </p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-bed"></i>
                            <p>Number of bedrooms: {{ $post->bedrooms }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-bolt"></i>
                            <p>Electricity price: {{ $post->electricity_price ?? 'As specified by landlord' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-bath"></i>
                            <p>Number of bathrooms: {{ $post->bathrooms }} </p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-wifi"></i>
                            <p>Internet price: {{ $post->internet_price ?? 'As specified by landlord' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-tint"></i>
                            <p>Water price: {{ $post->water_price ?? 'As specified by landlord' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-clock"></i>
                            <p>Service price: {{ $post->service_price ?? 'As specified by landlord' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-video"></i>
                            <p>Amenities: {{ $post->utilities ?? 'Basic' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-couch"></i>
                            <p>Furniture: {{ $post->furniture ?? 'Basic' }}</p>
                        </div>
                        <div class="feature">
                            <i class="fas fa-couch"></i>
                            <p>Furniture: {{ $post->furniture ?? 'Basic' }}</p>
                        </div>
                    </div>

                    {{-- Google Maps --}}
                    @if ($post->latitude && $post->longitude)
                    <h2 class="section-title">Location</h2>
                    <div id="map" style="height: 400px; width: 100%;"></div>
                    @endif

                    <!-- Comments Section -->
                    <h2 class="section-title">Comments</h2>
                    <div class="comments">
                        <!-- Comment list -->
                        <div class="comment-list">
                            @foreach ($post->comments->whereNull('parent_id')->sortByDesc('created_at') as $comment)
                            <div class="comment">
                                <div class="comment-header">
                                    <div class="comment-author-info">
                                        <strong>{{ $comment->user->name }}</strong>
                                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    @auth
                                    @if (Auth::id() === $comment->user_id)
                                    <button class="btn-action edit-btn"
                                        onclick="toggleEditForm({{ $comment->id }}, event)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    @endif
                                    @endauth
                                </div>

                                <div class="comment-content" id="comment-text-{{ $comment->id }}">
                                    {{ $comment->content }}
                                </div>

                                <!-- Edit form -->
                                @auth
                                @if (Auth::id() === $comment->user_id)
                                <form action="{{ route('landlord.comments.update', $comment->id) }}" method="POST"
                                    class="edit-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="content" required>{{ $comment->content }}</textarea>
                                    <div class="form-actions">
                                        <button type="submit" class="btn-submit">Save</button>
                                        <button type="button" class="btn-cancel"
                                            onclick="toggleEditForm(event, {{ $comment->id }})">Cancel</button>
                                    </div>
                                </form>
                                @endif
                                @endauth

                                <!-- Reply button -->
                                <div class="reply-actions">
                                    @auth
                                    @if (auth()->user()->role === 'landlord' && $comment->user->role === 'customer')
                                    <button class="btn-action reply-btn" onclick="toggleReplyForm({{ $comment->id }})">
                                        <i class="fas fa-reply"></i> Reply
                                    </button>
                                    @endif
                                    @endauth

                                    @if ($comment->replies->count() > 0)
                                    <button class="btn-action toggle-replies"
                                        onclick="toggleReplies({{ $comment->id }})" id="toggle-btn-{{ $comment->id }}">
                                        <i class="fas fa-comments"></i>
                                        <span class="toggle-text">View {{ $comment->replies->count() }} replies</span>
                                        <i class="fas fa-chevron-down toggle-icon"></i>
                                    </button>
                                    @endif
                                </div>

                                <!-- Reply form -->
                                @auth
                                @if (auth()->user()->role === 'landlord' && $comment->user->role === 'customer')
                                <form action="{{ route('landlord.comments.store', $post->id) }}" method="POST"
                                    class="reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <input type="hidden" name="post_type" value="landlord">
                                    <input type="hidden" name="reply_to" value="{{ $comment->user->name }}">
                                    <textarea name="content" placeholder="Write your reply..." required></textarea>
                                    <div class="form-actions">
                                        <button type="submit" class="btn-submit">Send</button>
                                        <button type="button" class="btn-cancel"
                                            onclick="toggleReplyForm({{ $comment->id }})">Cancel</button>
                                    </div>
                                </form>
                                @endif
                                @endauth

                                <!-- Reply list -->
                                <div class="replies" id="replies-{{ $comment->id }}" style="display: none;">
                                    @foreach ($comment->replies->sortByDesc('created_at') as $reply)
                                    <div class="comment reply level-2">
                                        <div class="comment-header">
                                            <div class="comment-author-info">
                                                <strong>{{ $reply->user->name }}</strong>
                                                <span class="comment-time">{{ $reply->created_at->diffForHumans()
                                                    }}</span>
                                            </div>
                                            @auth
                                            @if (Auth::id() === $reply->user_id)
                                            <button class="btn-action edit-btn"
                                                onclick="toggleEditForm({{ $reply->id }}, event)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            @endif
                                            @endauth
                                        </div>

                                        <div class="comment-content" id="comment-text-{{ $reply->id }}">
                                            @if($reply->parent->user_id !== $reply->user_id)
                                            <span class="reply-to">Replying to {{ $reply->parent->user->name }}</span><br>
                                            @endif
                                            {{ $reply->content }}
                                        </div>

                                        @auth
                                        @if (Auth::id() === $reply->user_id)
                                        <form action="{{ route('landlord.comments.update', $reply->id) }}" method="POST"
                                            class="edit-form" id="edit-form-{{ $reply->id }}" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content" required>{{ $reply->content }}</textarea>
                                            <div class="form-actions">
                                                <button type="submit" class="btn-submit">Save</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleEditForm({{ $reply->id }}, event)">Cancel</button>
                                            </div>
                                        </form>
                                        @endif
                                        @endauth

                                        <!-- Reply button for reply -->
                                        <div class="reply-actions">
                                            @auth
                                            @if (auth()->user()->role === 'landlord' && $reply->user->role ===
                                            'customer')
                                            <button class="btn-action reply-btn"
                                                onclick="toggleReplyForm({{ $reply->id }})">
                                                <i class="fas fa-reply"></i> Reply
                                            </button>
                                            @endif
                                            @endauth

                                            @if ($reply->replies->count() > 0)
                                            <button class="btn-action toggle-replies"
                                                onclick="toggleReplies({{ $reply->id }})"
                                                id="toggle-btn-{{ $reply->id }}">
                                                <i class="fas fa-comments"></i>
                                                <span class="toggle-text">View {{ $reply->replies->count() }} replies</span>
                                                <i class="fas fa-chevron-down toggle-icon"></i>
                                            </button>
                                            @endif
                                        </div>

                                        <!-- Reply form for reply -->
                                        @auth
                                        @if (auth()->user()->role === 'landlord' && $reply->user->role === 'customer')
                                        <form action="{{ route('landlord.comments.store', $post->id) }}" method="POST"
                                            class="reply-form" id="reply-form-{{ $reply->id }}" style="display: none;">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                            <input type="hidden" name="post_type" value="landlord">
                                            <input type="hidden" name="reply_to" value="{{ $reply->user->name }}">
                                            <textarea name="content" placeholder="Write your reply..."
                                                required></textarea>
                                            <div class="form-actions">
                                                <button type="submit" class="btn-submit">Send</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleReplyForm({{ $reply->id }})">Cancel</button>
                                            </div>
                                        </form>
                                        @endif
                                        @endauth

                                        <!-- Reply list for reply (level 3) -->
                                        <div class="replies level-3-replies" id="replies-{{ $reply->id }}"
                                            style="display: none;">
                                            @foreach ($reply->replies->sortByDesc('created_at') as $replyLevel3)
                                            <div class="comment reply level-3">
                                                <div class="comment-header">
                                                    <div class="comment-author-info">
                                                        <strong>{{ $replyLevel3->user->name }}</strong>
                                                        <span class="comment-time">{{
                                                            $replyLevel3->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    @auth
                                                    @if (Auth::id() === $replyLevel3->user_id)
                                                    <button class="btn-action edit-btn"
                                                        onclick="toggleEditForm({{ $replyLevel3->id }}, event)">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    @endif
                                                    @endauth
                                                </div>

                                                <div class="comment-content" id="comment-text-{{ $replyLevel3->id }}">
                                                    @if($replyLevel3->parent->user_id !== $replyLevel3->user_id)
                                                    <span class="reply-to">Replying to {{ $replyLevel3->parent->user->name
                                                        }}</span><br>
                                                    @endif
                                                    {{ $replyLevel3->content }}
                                                </div>

                                                <!-- FORM EDIT for reply level 3 -->
                                                @auth
                                                @if (Auth::id() === $replyLevel3->user_id)
                                                <form action="{{ route('landlord.comments.update', $replyLevel3->id) }}"
                                                    method="POST" class="edit-form"
                                                    id="edit-form-{{ $replyLevel3->id }}" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                    <textarea name="content"
                                                        required>{{ $replyLevel3->content }}</textarea>
                                                    <div class="form-actions">
                                                        <button type="submit" class="btn-submit">Save</button>
                                                        <button type="button" class="btn-cancel"
                                                            onclick="toggleEditForm({{ $replyLevel3->id }}, event)">Cancel</button>
                                                    </div>
                                                </form>
                                                @endif
                                                @endauth
                                                {{--
                                                <!-- Reply button for reply level 3 (no additional reply view button) -->
                                                <div class="reply-actions">
                                                    @auth
                                                    @if (auth()->user()->role === 'landlord' && $replyLevel3->user->role
                                                    === 'customer')
                                                    <button class="btn-action reply-btn"
                                                        onclick="toggleReplyForm({{ $replyLevel3->id }})">
                                                        <i class="fas fa-reply"></i> Reply
                                                    </button>
                                                    @endif
                                                    @endauth
                                                </div>

                                                <!-- Reply form for reply level 3 -->
                                                @auth
                                                @if (auth()->user()->role === 'landlord' && $replyLevel3->user->role ===
                                                'customer')
                                                <form action="{{ route('landlord.comments.store', $post->id) }}"
                                                    method="POST" class="reply-form"
                                                    id="reply-form-{{ $replyLevel3->id }}" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="parent_id"
                                                        value="{{ $replyLevel3->id }}">
                                                    <input type="hidden" name="post_type" value="landlord">
                                                    <input type="hidden" name="reply_to"
                                                        value="{{ $replyLevel3->user->name }}">
                                                    <textarea name="content" placeholder="Write your reply..."
                                                        required></textarea>
                                                    <div class="form-actions">
                                                        <button type="submit" class="btn-submit">Send</button>
                                                        <button type="button" class="btn-cancel"
                                                            onclick="toggleReplyForm({{ $replyLevel3->id }})">Cancel</button>
                                                    </div>
                                                </form>
                                                @endif
                                                @endauth --}}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Google Maps API -->
@if ($post->latitude && $post->longitude)
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap">
</script>
<script>
    function initMap() {
            console.log("initMap called");
            var postLocation = { lat: parseFloat("{{ $post->latitude }}"), lng: parseFloat("{{ $post->longitude }}") };
            console.log("Post Location:", postLocation);
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: postLocation
            });
            var marker = new google.maps.Marker({
                position: postLocation,
                map: map
            });
        }

//comment
// Hàm toggle reply form
    function toggleReplyForm(commentId) {
        var form = document.getElementById("reply-form-" + commentId);
        if (form.style.display === "none" || form.style.display === "") {
            // Ẩn tất cả các form reply khác
            document.querySelectorAll('.reply-form').forEach(function(f) {
                if (f.id !== 'reply-form-' + commentId) f.style.display = 'none';
            });
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    // Hàm toggle edit form
    // function toggleEditForm(commentId) {
    //     var form = document.getElementById("edit-form-" + commentId);
    //     if (form.style.display === "none" || form.style.display === "") {
    //         document.querySelectorAll('.edit-form').forEach(f => {
    //             if (f.id !== 'edit-form-' + commentId) f.style.display = 'none';
    //         });
    //         form.style.display = "block";
    //     } else {
    //         form.style.display = "none";
    //     }
    // }

    function toggleEditForm(commentId, event = null) {
    // Ngăn chặn hành vi mặc định nếu có event
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    console.log('Toggle edit form for comment:', commentId);
    
    // Ẩn tất cả các form edit và reply khác
    document.querySelectorAll('.edit-form, .reply-form').forEach(form => {
        if (form.id !== 'edit-form-' + commentId) {
            form.style.display = 'none';
        }
    });
    
    // Toggle form hiện tại
    const form = document.getElementById('edit-form-' + commentId);
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        
        // Focus vào textarea nếu form được hiển thị
        if (form.style.display === 'block') {
            const textarea = form.querySelector('textarea');
            if (textarea) {
                textarea.focus();
                // Đặt cursor ở cuối nội dung
                textarea.setSelectionRange(textarea.value.length, textarea.value.length);
            }
        }
    } else {
        console.error('Form not found with ID:', 'edit-form-' + commentId);
    }
}

    // Hàm toggle replies
    function toggleReplies(commentId) {
        var repliesDiv = document.getElementById("replies-" + commentId);
        var toggleBtn = document.getElementById("toggle-btn-" + commentId);
        var toggleIcon = toggleBtn.querySelector('.toggle-icon');
        var toggleText = toggleBtn.querySelector('.toggle-text');
        
        if (repliesDiv.style.display === "none" || repliesDiv.style.display === "") {
            repliesDiv.style.display = "block";
            toggleText.textContent = "Collapse";
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        } else {
            repliesDiv.style.display = "none";
            toggleText.textContent = "View " + repliesDiv.querySelectorAll('.reply').length + " replies";
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
        }
    }

    //amh 
document.addEventListener("DOMContentLoaded", function () {
    let currentIndex = {};

    function toggleDetails(postId) {
    console.log("Toggle details for post:", postId); // Kiểm tra xem hàm có được gọi không
    let details = document.getElementById('details-' + postId);
    let btnShow = document.getElementById('btn-show-' + postId);

    if (details.style.display === 'none') {
        details.style.display = 'block';
        btnShow.textContent = 'Collapse';
    } else {
        details.style.display = 'none';
        btnShow.textContent = 'View more';
    }
}

    function showAllImages(postId) {
        let modal = document.getElementById('image-modal-' + postId);
        if (!modal) return;

        modal.classList.add("active"); // Hiển thị modal
        currentIndex[postId] = 0; // Bắt đầu từ ảnh đầu tiên
        showImage(postId, currentIndex[postId]);

        // Đóng modal khi bấm ra ngoài
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                closeModal(postId);
            }
        });
    }

    function closeModal(postId) {
        let modal = document.getElementById('image-modal-' + postId);
        if (modal) {
            modal.classList.remove("active"); // Ẩn modal
        }
    }

    function showImage(postId, index) {
        let carouselItems = document.querySelectorAll(`#image-modal-${postId} .carousel-item`);
        if (!carouselItems.length) return;

        carouselItems.forEach((item, i) => {
            item.style.display = i === index ? 'block' : 'none';
        });
    }

    function prevImage(postId) {
        let carouselItems = document.querySelectorAll(`#image-modal-${postId} .carousel-item`);
        if (!carouselItems.length) return;

        currentIndex[postId] = (currentIndex[postId] - 1 + carouselItems.length) % carouselItems.length;
        showImage(postId, currentIndex[postId]);
    }

    function nextImage(postId) {
        let carouselItems = document.querySelectorAll(`#image-modal-${postId} .carousel-item`);
        if (!carouselItems.length) return;

        currentIndex[postId] = (currentIndex[postId] + 1) % carouselItems.length;
        showImage(postId, currentIndex[postId]);
    }
        // Gán các hàm vào `window` để có thể gọi từ HTML
        window.showAllImages = showAllImages;
    window.closeModal = closeModal;
    window.prevImage = prevImage;
    window.nextImage = nextImage;
    window.toggleDetails = toggleDetails;
});


//xu ly binh luan bi cam
    // Lấy danh sách từ cấm từ PHP
    const bannedWords = @json(\App\Models\BannedWord::pluck('word')->toArray());

    // Hàm kiểm tra từ cấm
    function checkBannedWords(content) {
        const lowerContent = content.toLowerCase().normalize('NFC');
        const foundWords = bannedWords.filter(word => {
            const regex = new RegExp('\\b' + word.toLowerCase().normalize('NFC') + '\\b', 'i');
            return regex.test(lowerContent);
        });
        return foundWords;
    }

    // Xử lý tất cả các form bình luận
    document.addEventListener('DOMContentLoaded', function() {
        const allForms = document.querySelectorAll('form[action*="/comments"]');
        
        allForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const textarea = this.querySelector('textarea[name="content"]');
                const content = textarea.value;
                const foundWords = checkBannedWords(content);
                
                if (foundWords.length > 0) {
                    alert(`Content contains inappropriate words: ${foundWords.join(', ')}`);
                    textarea.focus();
                    return false;
                }
                
                this.submit();
            });
        });
    });
</script>
@endif

@endsection