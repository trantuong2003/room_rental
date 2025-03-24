<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\ModerationPostController;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Landlord\PackageController;
use App\Http\Controllers\Landlord\PaymentController;
use App\Http\Controllers\Landlord\HistoryController;
use App\Http\Controllers\Landlord\CreatePostController;
use App\Http\Controllers\Landlord\PostController;
use App\Http\Controllers\Landlord\HomeController;
use App\Http\Controllers\Landlord\SubscriptionsController;

//customer
use App\Http\Controllers\Customer\HomeCustomerController;
use App\Http\Controllers\Customer\DetailPostController;
use App\Http\Controllers\Customer\FavoritePostController;

use App\Http\Controllers\CommentController;




// Route::get('/favourite', function () {
//     return view('customer/favourite');
// });

// Route::get('/postcustomer', function () {
//     return view('customer/post');
// });

// Route::get('/messagecustomer', function () {
//     return view('customer/message');
// });



// Route::prefix('admin')->middleware('auth.admin')->group(function () {
//     Route::get('/subscription', function () {
//         return view('admin.subscription');
//     });
// });




/* router of customer*/

Route::middleware(['auth.customer', 'verified'])->group(function () {
    Route::prefix('customer')->group(function () {
        Route::get('/', [HomeCustomerController::class, 'home'])->name('customer.home');

        //post
        Route::prefix('posts')->group(function () {
            Route::get('/detail/{id}', [DetailPostController::class, 'detailPost'])->name('customer.post.detail');
            Route::post('/toggle-favorite-home', [HomeCustomerController::class, 'toggleFavorite'])->name('customer.post.toggleFavorite');
            Route::post('/toggle-favorite-detail', [DetailPostController::class, 'toggleFavorite'])->name('customer.detail.toggleFavorite');
            Route::get('/favorites', [FavoritePostController::class, 'showFavorites'])->name('customer.favorites');
            Route::post('/toggle-favorite-detail-page', [FavoritePostController::class, 'toggleFavorite'])->name('customer.favorite.toggleFavorite');

            // 👉 Route để bình luận (customer)
            Route::post('/{post}/comments', [CommentController::class, 'store'])->name('customer.comments.store');
            Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('customer.comments.update');

        });
    });
});



/* router of admin*/
Route::middleware('auth')->group(function () {
    Route::prefix('admin')->middleware('auth.admin')->group(function () {
        Route::get('/', function () {
            return view('admin.dashbroad');
        });

        //subscription package
        Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscription', [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::get('/subscription/{id}/edit', [SubscriptionController::class, 'edit'])->name('subscriptions.edit');
        Route::put('/subscription/{id}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
        Route::delete('/subscription/{id}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');


        //post moderation
        Route::get('/moderation_post', [ModerationPostController::class, 'showPost'])->name('moderation.index');
        Route::patch('/moderation_post/{id}/approve', [ModerationPostController::class, 'approve'])->name('posts.approve');
        Route::patch('/moderation_post/{id}/reject', [ModerationPostController::class, 'reject'])->name('posts.reject');
    });
});



/* router of landlord*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('landlord')->middleware('auth.landlord')->group(function () {
        Route::get('/', [HomeController::class, 'home'])->name('landord.home');

        Route::get('/subscription', [PackageController::class, 'index'])->name('landlord.package');

        /*payment */
        Route::post('/vnpaypayment', [PaymentController::class, 'vnpay_payment'])->name('vnpay.payment');
        Route::get('/vnpay-return', [PaymentController::class, 'vnpay_return'])->name('vnpay.return');

        /*Payment History */
        Route::get('/history', [HistoryController::class, 'ShowHistory']);

        /*Create post */
        // Route::get('/create_post', [CreatePostController::class, 'index'])->name('landlord.posts.index');
        Route::prefix('posts')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('landlord.posts.index'); // Danh sách bài đăng
            Route::get('/detail{id}', [PostController::class, 'detail'])->name('landlord.posts.detail'); // chitiet
            Route::get('/create', [CreatePostController::class, 'create'])->name('landlord.posts.create'); // Form tạo bài đăng
            Route::post('/create', [CreatePostController::class, 'store'])->name('landlord.posts.store'); // Lưu bài đăng
            Route::get('/{id}/edit', [CreatePostController::class, 'edit'])->name('landlord.posts.edit');
            Route::put('/{id}', [CreatePostController::class, 'update'])->name('landlord.posts.update');
            Route::delete('/{id}', [CreatePostController::class, 'destroy'])->name('landlord.posts.destroy');

            // 👉 Route để bình luận (landlord)
            Route::post('/{post}/comments', [CommentController::class, 'store'])->name('landlord.comments.store');
            Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('landlord.comments.update');
        });

        Route::get('/subscription/remaining-posts', [SubscriptionsController::class, 'getRemainingPosts'])
            ->name('subscription.remaining_posts');
    });
});



Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route xác nhận email
Route::get('/email/verify', function () {
    return view('account.verify-email');
})->middleware('auth')->name('verification.notice');

// router gủi mail xác nhận
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/login')->with('message', 'Email của bạn đã được xác nhận!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Route gửi lại email xác nhận
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Email xác nhận đã được gửi lại!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//router kiểm tra đã verify tài khoản chưa
// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/', function () {
//         return view('customer.home');
//     });

//     Route::middleware('auth.landlord')->group(function () {
//         Route::get('/landlord', function () {
//             return view('landord.home');
//         });
//     });
// });


//reset password

// Form nhập email để lấy lại mật khẩu
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

// Gửi email đặt lại mật khẩu
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Form đặt lại mật khẩu mới
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Xử lý đặt lại mật khẩu
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
