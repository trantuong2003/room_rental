@extends('layouts.landord')

@section('content')
<div class="main">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="cardBox">
        <div class="card">
            <div>
                <div class="numbers">{{ $totalPosts }}</div>
                <div class="cardName">Total Posts</div>
            </div>
            <div class="iconBx">
                <ion-icon name="document-text-outline"></ion-icon>
            </div>
        </div>

        <div class="card">
            <div>
                <div class="numbers">{{ $subscriptionExpiry ?? 'No Active Subscription' }}</div>
                <div class="cardName">Subscription Expiry</div>
            </div>
            <div class="iconBx">
                <ion-icon name="calendar-outline"></ion-icon>
            </div>
        </div>

        <div class="card">
            <div>
                <div class="numbers">{{ $remainingPosts }}</div>
                <div class="cardName">Remaining Posts</div>
            </div>
            <div class="iconBx">
                <ion-icon name="cash-outline"></ion-icon>
            </div>
        </div>

        <div class="card">
            <div>
                <div class="numbers">{{ $recentTransactions->count() }}</div>
                <div class="cardName">Recent Transactions</div>
            </div>
            <div class="iconBx">
                <ion-icon name="wallet-outline"></ion-icon>
            </div>
        </div>
    </div>

    <div class="details">
        <div class="recentOrders">
            <div class="cardHeader">
                <h2>Recent Messages</h2>
                <a href="#" class="btn">View All</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <td>Sender</td>
                        <td>Message</td>
                        <td>Time</td>
                        <td>Status</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentMessages as $message)
                    <tr>
                        <td>{{ $message->sender->name }}</td>
                        <td>{{ Str::limit($message->message, 30) }}</td>
                        <td>{{ $message->created_at->diffForHumans() }}</td>
                        <td>
                            <span class="status {{ $message->is_read ? 'delivered' : 'pending' }}">
                                {{ $message->is_read ? 'Read' : 'Unread' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('landlord.chat.user', $message->sender_id) }}" class="btn">Reply</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="recentCustomers">
            <div class="cardHeader">
                <h2>Recent Comments</h2>
            </div>
            <table>
                @foreach($recentComments as $comment)
                <tr>
                    <td width="60px">
                        <div class="imgBx">
                            <img src="{{ $comment->user->profile_photo_url ?? 'assets/image/customer01.jpg' }}" alt="">
                        </div>
                    </td>
                    <td>
                        <h4>{{ $comment->user->name }} <br>
                            <span>{{ Str::limit($comment->content, 20) }}</span>
                        </h4>
                    </td>
                    <td>
                        <a href="{{ route('landlord.posts.detail', $comment->commentable_id) }}" class="btn">Reply</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection