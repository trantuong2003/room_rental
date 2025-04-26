<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannedWord;
use Illuminate\Http\Request;

class BannedWordController extends Controller
{
    // Hiển thị trang quản lý
    public function index()
    {
        return view('admin.banned_word');
    }

    // API lấy danh sách từ khóa
    public function getBannedWords(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);
        
        $words = BannedWord::when($search, function($query) use ($search) {
                return $query->where('word', 'like', '%'.$search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
            
        return response()->json($words);
    }

    // Thêm từ khóa mới
    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:255|unique:banned_words'
        ]);

        $word = BannedWord::create(['word' => $request->word]);

        return response()->json([
            'message' => 'Từ khóa đã được thêm thành công',
            'data' => $word
        ], 201);
    }

    // Lấy thông tin từ khóa để chỉnh sửa
    public function edit($id)
    {
        $word = BannedWord::findOrFail($id);
        return response()->json($word);
    }

    // Cập nhật từ khóa
    public function update(Request $request, $id)
    {
        $word = BannedWord::findOrFail($id);
        
        $request->validate([
            'word' => 'required|string|max:255|unique:banned_words,word,'.$word->id
        ]);

        $word->update(['word' => $request->word]);

        return response()->json([
            'message' => 'Từ khóa đã được cập nhật thành công'
        ]);
    }

    // Xóa từ khóa
    public function destroy($id)
    {
        $word = BannedWord::findOrFail($id);
        $word->delete();
        
        return response()->json([
            'message' => 'Từ khóa đã được xóa thành công'
        ]);
    }
}