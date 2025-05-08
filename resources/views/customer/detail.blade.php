@extends('layouts.customer')

@section('content')
<div class="detail_landlordpost_customer">

<div class="container">
    <div class="card_layout">
        <div class="left-section">
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
                    @foreach ($post->images->slice(1, 5) as $image)
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                    @endforeach
                    @if ($post->images->count() > 6)
                    <div class="see-more-overlay" onclick="showAllImages({{ $post->id }})">
                        <span>+{{ $post->images->count() - 4 }}</span>
                        <p>View more</p>
                    </div>
                    @endif
                </div>
            </div>
            <!-- Modal for displaying all images -->
            <div id="image-modal-{{ $post->id }}" class="image-modal hidden">
                <div class="modal-content">
                    <span class="close" onclick="closeModal({{ $post->id }})">×</span>
                    <div class="carousel">
                        @foreach ($post->images as $index => $image)
                        <div class="carousel-item" style="{{ $index === 0 ? 'display: block;' : 'display: none;' }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Image {{ $index + 1 }}">
                        </div>
                        @endforeach
                        <button class="carousel-control prev" onclick="prevImage({{ $post->id }})">❮</button>
                        <button class="carousel-control next" onclick="nextImage({{ $post->id }})">❯</button>
                    </div>
                </div>
            </div>
            <h1 class="header">{{ $post->title }}</h1>
            <p class="sub-header">{{ $post->address }}</p>
            <hr>
            <div class="info">
                <div class="price">
                    <p class="label">Price</p>
                    <p class="value">{{ $post->price }}</p>
                </div>
                <div>
                    <p class="label">Area</p>
                    <p class="value">{{ $post->acreage }}</p>
                </div>
                <div>
                    <p class="label">Bedrooms</p>
                    <p class="value">{{ $post->bedrooms }}</p>
                </div>
                <div class="icons">
                    <i class="fas fa-share-alt share-btn" data-url="{{ url()->current() }}"
                        style="cursor: pointer;"></i>
                    <div class="favorite-btn" data-post-id="{{ $post->id }}" data-post-type="landlord"
                        @auth onclick="toggleFavorite(event)"  @else onclick="showLoginAlert()" @endauth style="cursor: pointer;">
                        <i class="fas fa-heart" style="color: {{ $post->isFavorited ? 'red' : 'gray' }};"></i>
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
                    <p>Area: {{ $post->acreage }}</p>
                </div>
                <div class="feature">
                    <i class="fas fa-bed"></i>
                    <p>Number of bedrooms: {{ $post->bedrooms }}</p>
                </div>
                <div class="feature">
                    <i class="fas fa-bolt"></i>
                    <p>Electricity price: {{ $post->electricity_price ?? 'As per landlord' }}</p>
                </div>
                <div class="feature">
                    <i class="fas fa-bath"></i>
                    <p>Number of bathrooms: {{ $post->bathrooms }}</p>
                </div>
                <div class="feature">
                    <i class="fas fa-wifi"></i>
                    <p>Internet price: {{ $post->internet_price ?? 'As per landlord' }}</p>
                </div>
                <div class="feature">
                    <i class="fas fa-tint"></i>
                    <p>Water price: {{ $post->water_price ?? 'As per landlord' }}</p>
                </div>
                <div class="feature">
                    <i class="fas fa-clock"></i>
                    <p>Service price: {{ $post->service_price ?? 'As per landlord' }}</p>
                </div>
                <div class="feature">
                    <i class="fas fa-video"></i>
                    <p>Utilities: {{ $post->utilities ?? 'Basic' }}</p>
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
                @auth
                <div class="add-comment">
                    <h3>Add a comment</h3>
                    <form action="{{ route('customer.comments.store', $post->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_type" value="landlord">

                        <!-- Display errors if any -->
                        @if($errors->has('content') && session('failed_post_id') == $post->id)
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $errors->first('content') }}
                        </div>
                        @endif

                        <textarea name="content" placeholder="Write your comment..."
                            required>{{ old('content') }}</textarea>
                        <div class="form-actions">
                            <button type="submit" class="btn-submit">Post comment</button>
                        </div>
                    </form>
                </div>
                @else
                <div class="login-to-comment">
                    <p>Please <a href="{{ route('login') }}">log in</a> to comment</p>
                </div>
                @endauth
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
                            <button class="btn-action edit-btn" onclick="toggleEditForm({{ $comment->id }}, event)">
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
                        <form action="{{ route('customer.comments.update', $comment->id) }}" method="POST"
                            class="edit-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                            @csrf
                            @method('PUT')

                            @if($errors->has('content') && old('comment_id') == $comment->id)
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $errors->first('content') }}
                            </div>
                            @endif

                            <textarea name="content"
                                required>{{ old('comment_id') == $comment->id ? old('content') : $comment->content }}</textarea>
                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Save</button>
                                <button type="button" class="btn-cancel"
                                    onclick="toggleEditForm({{ $comment->id }}, event)">Cancel</button>
                            </div>
                        </form>
                        @endif
                        @endauth

                        <!-- Reply actions -->
                        <div class="reply-actions">
                            @auth
                            <button class="btn-action reply-btn" onclick="toggleReplyForm({{ $comment->id }})">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                            @endauth

                            @if ($comment->replies->count() > 0)
                            <button class="btn-action toggle-replies" onclick="toggleReplies({{ $comment->id }})"
                                id="toggle-btn-{{ $comment->id }}">
                                <i class="fas fa-comments"></i>
                                <span class="toggle-text">View {{ $comment->replies->count() }} replies</span>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </button>
                            @endif
                        </div>

                        <!-- Reply form -->
                        @auth
                        <form action="{{ route('customer.comments.store', $post->id) }}" method="POST"
                            class="reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <input type="hidden" name="post_type" value="landlord">
                            <textarea name="content" placeholder="Write your reply..." required></textarea>
                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Send</button>
                                <button type="button" class="btn-cancel"
                                    onclick="toggleReplyForm({{ $comment->id }})">Cancel</button>
                            </div>
                        </form>
                        @endauth

                        <!-- Replies list -->
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
                                <form action="{{ route('customer.comments.update', $reply->id) }}" method="POST"
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

                                <!-- Reply actions for reply -->
                                <div class="reply-actions">
                                    @auth
                                    <button class="btn-action reply-btn" onclick="toggleReplyForm({{ $reply->id }})">
                                        <i class="fas fa-reply"></i> Reply
                                    </button>
                                    @endauth

                                    @if ($reply->replies->count() > 0)
                                    <button class="btn-action toggle-replies" onclick="toggleReplies({{ $reply->id }})"
                                        id="toggle-btn-{{ $reply->id }}">
                                        <i class="fas fa-comments"></i>
                                        <span class="toggle-text">View {{ $reply->replies->count() }} replies</span>
                                        <i class="fas fa-chevron-down toggle-icon"></i>
                                    </button>
                                    @endif
                                </div>

                                <!-- Reply form for reply -->
                                @auth
                                <form action="{{ route('customer.comments.store', $post->id) }}" method="POST"
                                    class="reply-form" id="reply-form-{{ $reply->id }}" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                    <input type="hidden" name="post_type" value="landlord">
                                    <textarea name="content" placeholder="Write your reply..." required></textarea>
                                    <div class="form-actions">
                                        <button type="submit" class="btn-submit">Send</button>
                                        <button type="button" class="btn-cancel"
                                            onclick="toggleReplyForm({{ $reply->id }})">Cancel</button>
                                    </div>
                                </form>
                                @endauth
                                <!-- Replies list for reply (level 3) -->
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

                                        <!-- Edit form for reply level 3 -->
                                        @auth
                                        @if (Auth::id() === $replyLevel3->user_id)
                                        <form action="{{ route('customer.comments.update', $replyLevel3->id) }}"
                                            method="POST" class="edit-form" id="edit-form-{{ $replyLevel3->id }}"
                                            style="display: none;">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content" required>{{ $replyLevel3->content }}</textarea>
                                            <div class="form-actions">
                                                <button type="submit" class="btn-submit">Save</button>
                                                <button type="button" class="btn-cancel"
                                                    onclick="toggleEditForm({{ $replyLevel3->id }}, event)">Cancel</button>
                                            </div>
                                        </form>
                                        @endif
                                        @endauth
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
        <div class="right-section sticky">
            <div class="profile-landord">
                <div><img class="avatar" src="{{ asset('assets/image/customer02.jpg') }}" alt=""></div>
                <div class="infor">
                    <h2>{{ $post->user->name }}</h2>
                    <div class="address">
                        <div @auth onclick="goToChat({{ $post->user->id }})"  @else onclick="showLoginAlert()" @endauth>
                            <button class="zalo">Message now</button>
                        </div>
                        <button class="phone">Phone number: {{ $post->user->phone ?? 'Not updated' }}</button>
                    </div>
                </div>
            </div>
            <div class="warning">
                <p><i class="fas fa-exclamation-circle"></i> Do not make deposits or transactions before viewing the
                    property.</p>
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
        var postLocation = { lat: parseFloat("{{ $post->latitude }}"), lng: parseFloat("{{ $post->longitude }}") };
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: postLocation
        });
        var marker = new google.maps.Marker({
            position: postLocation,
            map: map
        });
    }

        function showLoginAlert() {
        alert("Please log in to perform this action!");
        window.location.href = "{{ route('login') }}";
    }
    
    // Toggle favorite
    async function toggleFavorite(event) {
        const button = event.currentTarget;
        const postId = button.getAttribute('data-post-id');
        const postType = button.getAttribute('data-post-type');
        const icon = button.querySelector('i');

        try {
            const response = await fetch("{{ route('customer.detail.toggleFavorite') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json"
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
        }
    }

    // Comment functions
    function toggleReplyForm(commentId) {
        var form = document.getElementById("reply-form-" + commentId);
        if (form.style.display === "none" || form.style.display === "") {
            // Hide all other reply forms
            document.querySelectorAll('.reply-form').forEach(function(f) {
                if (f.id !== 'reply-form-' + commentId) f.style.display = 'none';
            });
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    function toggleEditForm(commentId, event = null) {
        // Prevent default behavior if event exists
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        console.log('Toggle edit form for comment:', commentId);
        
        // Hide all other edit and reply forms
        document.querySelectorAll('.edit-form, .reply-form').forEach(form => {
            if (form.id !== 'edit-form-' + commentId) {
                form.style.display = 'none';
            }
        });
        
        // Toggle current form
        const form = document.getElementById('edit-form-' + commentId);
        if (form) {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            
            // Focus on textarea if form is displayed
            if (form.style.display === 'block') {
                const textarea = form.querySelector('textarea');
                if (textarea) {
                    textarea.focus();
                    // Set cursor to end of content
                    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                }
            }
        } else {
            console.error('Form not found with ID:', 'edit-form-' + commentId);
        }
    }

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

    // Share functionality
    function copyToClipboard(text) {
        // Create a temporary textarea
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed'; // Prevent display issues
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            // Execute copy
            document.execCommand('copy');
            return true;
        } catch (err) {
            console.error('Error copying:', err);
            return false;
        } finally {
            // Remove temporary textarea
            document.body.removeChild(textarea);
        }
    }

    // Handle share button click
    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            if (copyToClipboard(url)) {
                // Effect for successful copy
                this.classList.add('copied');
                const originalClass = this.className;
                
                // Temporarily change icon
                this.classList.remove('fa-share-alt');
                this.classList.add('fa-check');
                
                // Reset after 2 seconds
                setTimeout(() => {
                    this.className = originalClass;
                    this.classList.remove('copied');
                }, 2000);
                
                // Notification (optional)
                alert('Post link copied!');
            }
        });
    });

    // Image modal functions
    document.addEventListener("DOMContentLoaded", function () {
        let currentIndex = {};

        function toggleDetails(postId) {
            console.log("Toggle details for post:", postId); // Check if function is called
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

            modal.classList.add("active"); // Show modal
            currentIndex[postId] = 0; // Start from first image
            showImage(postId, currentIndex[postId]);

            // Close modal when clicking outside
            modal.addEventListener("click", function (event) {
                if (event.target === modal) {
                    closeModal(postId);
                }
            });
        }

        function closeModal(postId) {
            let modal = document.getElementById('image-modal-' + postId);
            if (modal) {
                modal.classList.remove("active"); // Hide modal
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

        // Assign functions to `window` for HTML access
        window.showAllImages = showAllImages;
        window.closeModal = closeModal;
        window.prevImage = prevImage;
        window.nextImage = nextImage;
        window.toggleDetails = toggleDetails;
    });

    // Banned words handling
    const bannedWords = @json(\App\Models\BannedWord::pluck('word')->toArray());

    // Function to check banned words
    function checkBannedWords(content) {
        const lowerContent = content.toLowerCase().normalize('NFC');
        const foundWords = bannedWords.filter(word => {
            const regex = new RegExp('\\b' + word.toLowerCase().normalize('NFC') + '\\b', 'i');
            return regex.test(lowerContent);
        });
        return foundWords;
    }

    // Handle all comment forms
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

        // Redirect to chat page with user ID
        function goToChat(userId) {
        window.location.href = "{{ route('customer.chat.user', ':userId') }}".replace(':userId', userId);
    }
</script>
@endif

@endsection

<style>
    .error-message {
        background-color: #ffe6e6;
        color: #cc0000;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 5px;
        border-left: 4px solid #cc0000;
    }

    .success-message {
        background-color: #e6ffe6;
        color: #006600;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 5px;
        border-left: 4px solid #006600;
    }
</style>