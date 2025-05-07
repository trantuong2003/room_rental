<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;
use Illuminate\Support\Facades\Auth;
use App\Models\LandlordPostImage;
use Illuminate\Support\Facades\Storage;
class CreatePostController extends Controller
{


    public function create()
    {
        return view('landord.landlord_create_posts');
    }

    /**
     * Lưu bài đăng mới vào database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|string', // Chuyển thành string để nhập đơn vị tiền tệ
            'address' => 'required|string',
            'latitude' => 'required|numeric', // Thêm tọa độ vĩ độ
            'longitude' => 'required|numeric', // Thêm tọa độ kinh độ
            'acreage' => 'required|string', // Đổi từ 'area' thành 'acreage' và cho phép nhập dạng "50m²"
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'electricity_price' => 'nullable|string', // Chuyển thành string
            'internet_price' => 'nullable|string', // Chuyển thành string
            'water_price' => 'nullable|string', // Chuyển thành string
            'service_price' => 'nullable|string', // Chuyển thành string
            'furniture' => 'nullable|string',
            'utilities' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:8192', // Tối đa 8MB mỗi ảnh
        ]);

        // Thêm user_id vào dữ liệu
        $data['user_id'] = Auth::id();


        try {
            $post = LandlordPost::createPost($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('uploads', 'public');
                    LandlordPostImage::create([
                        'landlord_post_id' => $post->id,
                        'image_path' => $path,
                    ]);
                }
            }

            return redirect()->route('landlord.posts.index')->with('success', 'Bài đăng đã được tạo thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $post = LandlordPost::findOrFail($id);
        return view('landord.editpost', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = LandlordPost::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'acreage' => 'required|string',
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'electricity_price' => 'nullable|string',
            'internet_price' => 'nullable|string',
            'water_price' => 'nullable|string',
            'service_price' => 'nullable|string',
            'furniture' => 'nullable|string',
            'utilities' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:8192',
        ]);

        $post->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('uploads', 'public');
                LandlordPostImage::create([
                    'landlord_post_id' => $post->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('landlord.posts.index')->with('success', 'Bài đăng đã được cập nhật thành công!');
    }

    public function deleteImages($postId)
    {
        $post = LandlordPost::findOrFail($postId);
    
        // Kiểm tra quyền truy cập - chỉ chủ bài đăng mới được xóa
        if ($post->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to delete these images.');
        }
    
        // Xóa tất cả hình ảnh liên quan
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    
        return redirect()->route('landlord.posts.edit', $postId)->with('success', 'All images of the post have been deleted successfully!');
    }
    public function destroy($id)
    {
        // \Log::info('destroy được gọi cho bài đăng ID: ' . $id);
        $post = LandlordPost::findOrFail($id);
    
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    
        $post->delete();
    
        return redirect()->route('landlord.posts.index')->with('success', 'Bài đăng và các ảnh đã được xóa thành công!');
    }
}
