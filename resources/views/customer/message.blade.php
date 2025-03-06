@extends('layouts/customer')

@section('content')

<div class="chat-container">
    <!-- Danh sách người dùng -->
    <div class="user-list">
        <h3>Danh sách</h3>
        <ul>
            <li onclick="openChat('Nguyễn Văn A')">Nguyễn Văn A</li>
            <li onclick="openChat('Trần Thị B')">Trần Thị B</li>
            <li onclick="openChat('Lê Văn C')">Lê Văn C</li>
        </ul>
    </div>

    <!-- Khung chat -->
    <div class="chat-box">
        <div class="chat-header">
            <h3 id="chatUserName">Chọn người để nhắn tin</h3>
        </div>
        <div class="chat-messages" id="chatMessages">
            <p>Chưa có tin nhắn.</p>
        </div>
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Nhập tin nhắn...">
            <button onclick="sendMessage()">Gửi</button>
        </div>
    </div>
</div>
@endsection