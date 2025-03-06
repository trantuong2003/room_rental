<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\SubscriptionController;

// Route::get('/detail', function () {
//     return view('customer/detail');
// });

// Route::get('/favourite', function () {
//     return view('customer/favourite');
// });

// Route::get('/postcustomer', function () {
//     return view('customer/post');
// });

// Route::get('/messagecustomer', function () {
//     return view('customer/message');
// });



Route::prefix('admin')->middleware('auth.admin')->group(function () {
    Route::get('/subscription', function () {
        return view('admin.subscription');
    });
});
Route::get('/subscription', function () {
    return view('landord/subscription');
});



/* router of customer*/
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('customer.home');
    });
    // Route::group(['middleware' => 'auth.admin'], function () {
    //     Route::get('/admin', function () {
    //         return view('admin.dashbroad');
    //     });
    // });
    // Route::group(['middleware' => 'auth.landlord'], function () {
    //     Route::get('/landlord', function () {
    //         return view('landord.home');
    //     });
    // });
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
    });
});


/* router of landlord*/
Route::middleware('auth')->group(function () {
    Route::prefix('landlord')->middleware('auth.landlord')->group(function () {
        Route::get('/', function () {
            return view('landord.home');
        });
    });
});



Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showForm']);
Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route xác nhận email
Route::get('/email/verify', function () {
    return view('verify-email');
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
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return view('customer.home');
    });

    Route::middleware('auth.landlord')->group(function () {
        Route::get('/landlord', function () {
            return view('landord.home');
        });
    });
});
