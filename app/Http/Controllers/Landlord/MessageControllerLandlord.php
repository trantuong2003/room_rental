<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class MessageControllerLandlord extends Controller
{
    public function index(Request $request, $userId = null)
    {
        // Lấy danh sách tin nhắn liên quan đến người dùng hiện tại
        $messages = Message::where(function ($query) {
            $query->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
        })->with(['sender', 'receiver'])->orderBy('created_at', 'desc')->get();

        // Nhóm tin nhắn theo người dùng khác (người không phải Auth::id())
        $latestMessages = $messages->groupBy(function ($message) {
            return $message->sender_id == Auth::id() ? $message->receiver_id : $message->sender_id;
        })->map(function ($group) {
            return $group->first(); // Lấy tin nhắn mới nhất cho mỗi người dùng
        });

        // Lấy danh sách ID người dùng từ tin nhắn
        $userIds = $latestMessages->keys()->unique();

        // Đếm số tin nhắn chưa đọc cho mỗi người dùng
        $unreadCounts = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->selectRaw('sender_id, COUNT(*) as unread_count')
            ->pluck('unread_count', 'sender_id');

        // Lấy danh sách người dùng đã từng nhắn tin
        $users = User::whereIn('id', $userIds)
            ->get()
            ->map(function ($user) use ($latestMessages, $unreadCounts) {
                $user->last_message = $latestMessages->get($user->id);
                $user->unread_count = $unreadCounts->get($user->id, 0);
                return $user;
            })->sortByDesc(function ($user) {
                return $user->last_message ? $user->last_message->created_at->timestamp : 0;
            })->values();

        // Nếu có từ khóa tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $users = $users->filter(function ($user) use ($search) {
                return stripos($user->name, $search) !== false;
            })->values();
        }

        // Lấy tin nhắn của người dùng được chọn (nếu có)
        $selectedMessages = [];
        if ($userId) {
            $selectedMessages = Message::where(function ($query) use ($userId) {
                $query->where('sender_id', Auth::id())->where('receiver_id', $userId);
            })->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)->where('receiver_id', Auth::id());
            })->orderBy('created_at', 'asc')->with('sender')->get();

            // Đánh dấu tin nhắn đã đọc
            Message::where('sender_id', $userId)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('landord.message', compact('users', 'selectedMessages', 'userId'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return redirect()->route('landlord.chat.user', ['userId' => $request->receiver_id])
            ->with('success', 'Tin nhắn đã được gửi!');
    }
}