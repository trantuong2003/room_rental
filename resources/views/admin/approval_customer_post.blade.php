@extends('layouts.admin')

@section('content')
<div class="approval_customer_post">
    <div class="post-header">
        <h2>Quản lý bài đăng</h2>
    </div>
    
    <div class="post-filters">
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">Tất cả</button>
            <button class="filter-btn" data-filter="pending">Chờ duyệt</button>
            <button class="filter-btn" data-filter="approved">Đã duyệt</button>
            <button class="filter-btn" data-filter="rejected">Từ chối</button>
        </div>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Tìm kiếm bài đăng...">
            <i class="fas fa-search search-icon"></i>
        </div>
    </div>
    
    <div class="post-grid">
        @foreach($posts as $post)
        <div class="post-card {{ $post->status }}" id="post-{{ $post->id }}">
            <div class="post-card-header">
                <h3 class="post-title">{{ $post->title }}</h3>
                <div class="post-meta">
                    <div class="post-author">
                        <i class="fas fa-user"></i> {{ $post->user->name }}
                    </div>
                    <div class="post-time">
                        <i class="far fa-clock"></i> {{ $post->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            
            <div class="post-content">
                <div class="post-content-preview">
                    {{ Str::limit($post->content, 150) }}
                    @if(strlen($post->content) > 150)
                        <button class="read-more-btn" data-content="{{ $post->content }}">Xem thêm</button>
                    @endif
                </div>
            </div>
            
            <div class="post-footer">
                <div class="post-status">
                    @if($post->status === 'pending')
                        <span class="status-badge pending">Chờ duyệt</span>
                    @elseif($post->status === 'approved')
                        <span class="status-badge approved">Đã duyệt</span>
                    @else
                        <span class="status-badge rejected">Từ chối</span>
                    @endif
                </div>
                
                <div class="post-actions">
                    <form action="{{ route('moderation.customer.approve', $post->id) }}" 
                          method="POST" class="action-form approve-form">
                        @csrf
                        <button type="button" class="action-btn approve-btn" data-id="{{ $post->id }}">
                            <i class="fas fa-check"></i> Duyệt
                        </button>
                    </form>
                    
                    <form action="{{ route('moderation.customer.reject', $post->id) }}" 
                          method="POST" class="action-form reject-form">
                        @csrf
                        <button type="button" class="action-btn reject-btn" data-id="{{ $post->id }}">
                            <i class="fas fa-times"></i> Từ chối
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="pagination-container">
        {{ $posts->links() }}
    </div>
    
    <!-- Modal for full content -->
    <div class="content-modal" id="contentModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3 class="modal-title">Nội dung đầy đủ</h3>
            <div class="modal-body"></div>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div class="confirmation-modal" id="confirmationModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3 class="modal-title" id="confirmationTitle">Xác nhận</h3>
            <div class="modal-body" id="confirmationMessage">
                Bạn có chắc chắn muốn thực hiện hành động này?
            </div>
            <div class="modal-footer">
                <button class="modal-btn cancel-btn" id="cancelAction">Hủy</button>
                <button class="modal-btn confirm-btn" id="confirmAction">Xác nhận</button>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div class="toast-notification" id="toastNotification">
        <div class="toast-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="toast-message" id="toastMessage"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const postCards = document.querySelectorAll('.post-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter posts
            postCards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                } else {
                    if (card.classList.contains(filter)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('.search-input');
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        
        postCards.forEach(card => {
            const title = card.querySelector('.post-title').textContent.toLowerCase();
            const content = card.querySelector('.post-content-preview').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || content.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Content Modal functionality
    const contentModal = document.getElementById('contentModal');
    const modalBody = contentModal.querySelector('.modal-body');
    const closeContentModal = contentModal.querySelector('.close-modal');
    const readMoreButtons = document.querySelectorAll('.read-more-btn');
    
    readMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const content = this.getAttribute('data-content');
            // Format the content with proper line breaks
            const formattedContent = content.replace(/\n/g, '<br>');
            modalBody.innerHTML = formattedContent;
            contentModal.style.display = 'block';
        });
    });
    
    closeContentModal.addEventListener('click', function() {
        contentModal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target === contentModal) {
            contentModal.style.display = 'none';
        }
        if (event.target === confirmationModal) {
            confirmationModal.style.display = 'none';
        }
    });
    
    // Confirmation Modal functionality
    const confirmationModal = document.getElementById('confirmationModal');
    const confirmationTitle = document.getElementById('confirmationTitle');
    const confirmationMessage = document.getElementById('confirmationMessage');
    const confirmButton = document.getElementById('confirmAction');
    const cancelButton = document.getElementById('cancelAction');
    const closeConfirmModal = confirmationModal.querySelector('.close-modal');
    
    // Toast notification
    const toastNotification = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');
    
    function showToast(message, isSuccess = true) {
        toastMessage.textContent = message;
        toastNotification.className = 'toast-notification';
        if (isSuccess) {
            toastNotification.classList.add('success');
        } else {
            toastNotification.classList.add('error');
        }
        toastNotification.classList.add('show');
        
        setTimeout(() => {
            toastNotification.classList.remove('show');
        }, 3000);
    }
    
    // Approve button click
    const approveButtons = document.querySelectorAll('.approve-btn');
    approveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            const form = this.closest('form');
            
            confirmationTitle.textContent = 'Xác nhận duyệt';
            confirmationMessage.textContent = 'Bạn có chắc chắn muốn duyệt bài đăng này?';
            confirmationModal.style.display = 'block';
            
            // Store the form to submit later
            confirmButton.setAttribute('data-form', postId);
            confirmButton.setAttribute('data-action', 'approve');
        });
    });
    
    // Reject button click
    const rejectButtons = document.querySelectorAll('.reject-btn');
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id');
            const form = this.closest('form');
            
            confirmationTitle.textContent = 'Xác nhận từ chối';
            confirmationMessage.textContent = 'Bạn có chắc chắn muốn từ chối bài đăng này?';
            confirmationModal.style.display = 'block';
            
            // Store the form to submit later
            confirmButton.setAttribute('data-form', postId);
            confirmButton.setAttribute('data-action', 'reject');
        });
    });
    
    // Confirm action
    confirmButton.addEventListener('click', function() {
        const postId = this.getAttribute('data-form');
        const action = this.getAttribute('data-action');
        const postCard = document.getElementById('post-' + postId);
        
        // Close the modal
        confirmationModal.style.display = 'none';
        
        // Simulate form submission with AJAX
        // In a real application, you would submit the form to the server
        // Here we're just updating the UI for demonstration
        
        if (action === 'approve') {
            // Update status badge
            const statusBadge = postCard.querySelector('.status-badge');
            statusBadge.className = 'status-badge approved';
            statusBadge.textContent = 'Đã duyệt';
            
            // Update card class
            postCard.className = postCard.className.replace(/pending|rejected/g, 'approved');
            
            showToast('Bài đăng đã được duyệt thành công!');
            
            // In a real application, submit the form
            const form = postCard.querySelector('.approve-form');
            form.submit();
        } else if (action === 'reject') {
            // Update status badge
            const statusBadge = postCard.querySelector('.status-badge');
            statusBadge.className = 'status-badge rejected';
            statusBadge.textContent = 'Từ chối';
            
            // Update card class
            postCard.className = postCard.className.replace(/pending|approved/g, 'rejected');
            
            showToast('Bài đăng đã bị từ chối!');
            
            // In a real application, submit the form
            const form = postCard.querySelector('.reject-form');
            form.submit();
        }
    });
    
    // Cancel action
    cancelButton.addEventListener('click', function() {
        confirmationModal.style.display = 'none';
    });
    
    // Close confirmation modal
    closeConfirmModal.addEventListener('click', function() {
        confirmationModal.style.display = 'none';
    });
});
</script>
@endsection
