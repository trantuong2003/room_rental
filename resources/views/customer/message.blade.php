@extends('layouts.customer')

@section('content')
<div class="main">
    <div class="message-container">
        <!-- Conversations List -->
        <div class="conversations-list">
            <div class="conversations-header">
                <h2>Messages</h2>
                {{-- <form action="{{ route('customer.chat') }}" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search for users..." value="{{ request('search') }}">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form> --}}
            </div>
            <div class="conversations">
                @forelse ($users as $user)
                    <div class="conversation {{ $userId == $user->id ? 'active' : '' }}"
                         onclick="window.location='{{ route('customer.chat.user', $user->id) }}'">
                        <div class="conversation-avatar">
                            <img src="{{ $user->avatar ?? 'https://randomuser.me/api/portraits/men/32.jpg' }}" alt="User">
                        </div>
                        <div class="conversation-info">
                            <h3>{{ $user->name }}</h3>
                            <p>{{ $user->last_message ? Str::limit($user->last_message->message, 30) : 'No messages yet' }}</p>
                        </div>
                        <div class="conversation-meta">
                            <span class="time">
                                {{ $user->last_message ? $user->last_message->created_at->diffForHumans() : '' }}
                            </span>
                            @if ($user->unread_count > 0)
                                <span class="unread">{{ $user->unread_count }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="no-conversation">No conversations yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Message Area -->
        <div class="message-area" style="{{ $userId ? '' : 'display: none;' }}">
            @if ($userId)
                @php
                    $selectedUser = App\Models\User::find($userId);
                @endphp
                <div class="message-header">
                    <div class="user-info">
                        <div class="user-avatar">
                            <img src="{{ $selectedUser->avatar ?? 'https://randomuser.me/api/portraits/men/32.jpg' }}" alt="User">
                        </div>
                        <div class="user-details">
                            <h3>{{ $selectedUser->name }}</h3>
                        </div>
                    </div>
                </div>

                <div class="messages" id="messages-container">
                    @php
                        $currentDate = null;
                    @endphp
                    @foreach ($selectedMessages as $message)
                        @php
                            $messageDate = $message->created_at->toDateString();
                            if ($messageDate !== $currentDate) {
                                $currentDate = $messageDate;
                                echo '<div class="date-divider"><span>' . $message->created_at->format('m/d/Y') . '</span></div>';
                            }
                        @endphp
                        <div class="message {{ $message->sender_id == Auth::id() ? 'sent' : 'received' }}">
                            @if ($message->sender_id != Auth::id())
                                <div class="message-avatar">
                                    <img src="{{ $message->sender->avatar ?? 'https://randomuser.me/api/portraits/men/32.jpg' }}" alt="User">
                                </div>
                            @endif
                            <div class="message-content">
                                <div class="message-bubble">
                                    <p>{{ $message->message }}</p>
                                </div>
                                <div class="message-time">{{ $message->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="message-input-container">
                    <form action="{{ route('customer.messages.send') }}" method="POST" class="message-form">
                        @csrf
                        <div class="message-input-wrapper">
                            <input type="hidden" name="receiver_id" value="{{ $userId }}">
                            <input type="text" name="message" class="message-input-field" placeholder="Type your message..." required>
                            <button type="submit" class="message-send-btn">
                                <i class="fas fa-paper-plane message-send-icon"></i>
                                <span class="message-send-text">Send</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Empty State -->
        <div class="empty-state" style="{{ $userId ? 'display: none;' : '' }}">
            <div class="empty-state-content">
                <i class="far fa-comment-dots empty-state-icon"></i>
                <h2>Your Messages</h2>
                <p>Select a conversation to start messaging</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    
    function scrollToBottom() {
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    scrollToBottom();
});
</script>
@endsection