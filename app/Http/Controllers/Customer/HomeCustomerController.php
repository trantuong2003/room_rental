<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandlordPost;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\CustomerPost;

class HomeCustomerController extends Controller
{
    public function home(Request $request)
    {
        $query = LandlordPost::with('images', 'user')
            ->where('status', 'approved');

        // Tìm kiếm theo vị trí hoặc tên bài đăng
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo loại nhà đất (nếu có cột property_type)
        if ($propertyType = $request->query('property_type')) {
            $query->where('property_type', $propertyType);
        }

        // Lọc theo khu vực
        if ($area = $request->query('area')) {
            $query->where('address', 'like', '%' . $area . '%');
        }

        // Lọc theo khoảng giá
        if ($priceRange = $request->query('price_range')) {
            [$minPrice, $maxPrice] = explode('-', $priceRange);
            $query->whereRaw('CAST(price AS DECIMAL(15,2)) BETWEEN ? AND ?', [$minPrice, $maxPrice]);
        }

        // Lọc theo diện tích
        if ($acreageRange = $request->query('acreage_range')) {
            [$minAcreage, $maxAcreage] = explode('-', $acreageRange);
            $query->whereRaw('CAST(acreage AS DECIMAL(15,2)) BETWEEN ? AND ?', [$minAcreage, $maxAcreage]);
        }

        // Lấy danh sách bài đăng
        $landlordPosts = $query->latest()->get();

        $userId = Auth::id();

        $landlordPosts->each(function ($post) use ($userId) {
            $post->isFavorited = $userId ? Favorite::where('user_id', $userId)
                ->where('favoriteable_id', $post->id)
                ->where('favoriteable_type', LandlordPost::class)
                ->exists() : false;

            $post->type = 'landlord';
        });

        return view('customer.home', ['posts' => $landlordPosts]);
    }

    public function toggleFavorite(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để thêm vào danh sách yêu thích'], 401);
        }

        $request->validate([
            'post_id' => 'required|integer',
            'post_type' => 'required|in:landlord,customer'
        ]);

        $userId = Auth::id();
        $postId = $request->post_id;
        $postType = $request->post_type;

        $model = $postType === 'landlord' ? LandlordPost::class : CustomerPost::class;

        $favorite = Favorite::where('user_id', $userId)
            ->where('favoriteable_id', $postId)
            ->where('favoriteable_type', $model)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Favorite::create([
                'user_id' => $userId,
                'favoriteable_id' => $postId,
                'favoriteable_type' => $model
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}
