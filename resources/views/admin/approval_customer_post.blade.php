@extends('layouts.admin')

@section('content')
<div class="approval_customer_post">
    <div class="post-header">
        <h2>Manage posts</h2>
    </div>

    <div class="post-filters">
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="pending">Pending</button>
            <button class="filter-btn" data-filter="approved">Approval</button>
            <button class="filter-btn" data-filter="rejected">Reject</button>
        </div>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search posts...">
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
                @if($post->status === 'rejected' && $post->rejection_reason)
                <div class="rejection-reason">
                    <p class="rejection-title">Reason for rejection:</p>
                    <p class="rejection-text">{{ $post->rejection_reason }}</p>
                </div>
                @endif
            </div>

            <div class="post-footer">
                <div class="post-status">
                    @if($post->status === 'pending')
                    <span class="status-badge pending">Pending</span>
                    @elseif($post->status === 'approved')
                    <span class="status-badge approved">Approved</span>
                    @else
                    <span class="status-badge rejected">Rejected</span>
                    @endif
                </div>

                <div class="post-actions">
                    <form action="{{ route('moderation.customer.approve', $post->id) }}" method="POST"
                        class="action-form approve-form">
                        @csrf
                        <button type="button" class="action-btn approve-btn" data-id="{{ $post->id }}">
                            <i class="fas fa-check"></i> Approval
                        </button>
                    </form>

                    <button type="button" class="action-btn reject-btn" data-id="{{ $post->id }}">
                        <i class="fas fa-times"></i> Reject
                    </button>
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
            <span class="close-modal">×</span>
            <h3 class="modal-title">Full content</h3>
            <div class="modal-body"></div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="confirmation-modal" id="confirmationModal">
        <div class="modal-content">
            <span class="close-modal">×</span>
            <h3 class="modal-title" id="confirmationTitle">Confirm</h3>
            <div class="modal-body" id="confirmationMessage">
                Are you sure you want to perform this action?
            </div>
            <div class="modal-footer">
                <button class="modal-btn cancel-btn" id="cancelAction">Cancel</button>
                <button class="modal-btn confirm-btn" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Reject Reason Modal -->
    <div class="modal" id="rejectModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeRejectModal()">×</span>
            <h3 class="modal-title">Reason for rejection</h3>
            <form id="rejectForm" action="" method="POST">
                @csrf
                <div class="form-group">
                    <label for="rejection_reason">Please enter reason for rejection:</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="5" class="form-control" required
                        minlength="10" maxlength="500"></textarea>
                    <small class="text-muted">Minimum 10 characters, maximum 500 characters</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn cancel-btn" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="modal-btn confirm-btn">Confirm</button>
                </div>
            </form>
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

<style>
    .rejection-reason {
        margin: 10px 0;
        padding: 10px;
        background-color: #ffebee;
        border-left: 4px solid #f44336;
        border-radius: 4px;
    }

    .rejection-title {
        font-weight: bold;
        margin-bottom: 5px;
        color: #d32f2f;
    }

    .rejection-text {
        margin: 0;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 5px;
        width: 60%;
        max-width: 600px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chức năng lọc
        const filterButtons = document.querySelectorAll('.filter-btn');
        const postCards = document.querySelectorAll('.post-card');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
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
        
        // Chức năng tìm kiếm
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
        
        // Chức năng modal nội dung
        const contentModal = document.getElementById('contentModal');
        const modalBody = contentModal.querySelector('.modal-body');
        const closeContentModal = contentModal.querySelector('.close-modal');
        const readMoreButtons = document.querySelectorAll('.read-more-btn');
        
        readMoreButtons.forEach(button => {
            button.addEventListener('click', function() {
                const content = this.getAttribute('data-content');
                modalBody.innerHTML = content.replace(/\n/g, '<br>');
                contentModal.style.display = 'block';
            });
        });
        
        closeContentModal.addEventListener('click', function() {
            contentModal.style.display = 'none';
        });
        
        // Chức năng modal xác nhận
        const confirmationModal = document.getElementById('confirmationModal');
        const confirmationTitle = document.getElementById('confirmationTitle');
        const confirmationMessage = document.getElementById('confirmationMessage');
        const confirmButton = document.getElementById('confirmAction');
        const cancelButton = document.getElementById('cancelAction');
        const closeConfirmModal = confirmationModal.querySelector('.close-modal');
        
        // Chức năng modal từ chối
        const rejectModal = document.getElementById('rejectModal');
        const rejectForm = document.getElementById('rejectForm');
        const rejectionReason = document.getElementById('rejection_reason');
        
        function closeRejectModal() {
            rejectModal.style.display = 'none';
            rejectionReason.value = '';
        }
        
        // Thông báo toast
        const toastNotification = document.getElementById('toastNotification');
        const toastMessage = document.getElementById('toastMessage');
        
        function showToast(message, isSuccess = true) {
            toastMessage.textContent = message;
            toastNotification.className = 'toast-notification';
            toastNotification.classList.add(isSuccess ? 'success' : 'error');
            toastNotification.classList.add('show');
            setTimeout(() => {
                toastNotification.classList.remove('show');
            }, 3000);
        }
        
        // Sự kiện nút duyệt
        const approveButtons = document.querySelectorAll('.approve-btn');
        approveButtons.forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.getAttribute('data-id');
                confirmationTitle.textContent = 'Xác nhận duyệt';
                confirmationMessage.textContent = 'Bạn có chắc chắn muốn duyệt bài đăng này?';
                confirmationModal.style.display = 'block';
                
                confirmButton.onclick = function() {
                    const form = button.closest('form');
                    form.submit();
                    confirmationModal.style.display = 'none';
                };
            });
        });
        
        // Sự kiện nút từ chối
        const rejectButtons = document.querySelectorAll('.reject-btn');
        rejectButtons.forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.getAttribute('data-id');
                confirmationTitle.textContent = 'Xác nhận từ chối';
                confirmationMessage.textContent = 'Bạn có chắc chắn muốn từ chối bài đăng này?';
                confirmationModal.style.display = 'block';
                
                confirmButton.onclick = function() {
                    confirmationModal.style.display = 'none';
                    rejectForm.action = `/admin/moderation/customer/posts/${postId}/reject`; // Route đã sửa
                    rejectModal.style.display = 'block';
                };
            });
        });
        
        // Hủy hành động
        cancelButton.addEventListener('click', function() {
            confirmationModal.style.display = 'none';
        });
        
        // Đóng modal xác nhận
        closeConfirmModal.addEventListener('click', function() {
            confirmationModal.style.display = 'none';
        });
        
        window.addEventListener('click', function(event) {
            if (event.target === contentModal) {
                contentModal.style.display = 'none';
            }
            if (event.target === confirmationModal) {
                confirmationModal.style.display = 'none';
            }
            if (event.target === rejectModal) {
                closeRejectModal();
            }
        });
    });
</script>
@endsection