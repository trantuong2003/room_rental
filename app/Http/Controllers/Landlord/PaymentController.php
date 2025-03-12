<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function vnpay_payment(Request $request)
    {
        $data = $request->all();

        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return');
        $vnp_TmnCode = "WM6I50QP"; //Mã website tại VNPAY 
        $vnp_HashSecret = "241ZOFQ46R88O0TWLIA3TC9U9YH1WEXK"; //Chuỗi bí mật

        $vnp_TxnRef = time() . ""; // Mã đơn hàng duy nhất
        $vnp_OrderInfo = "Payment for package";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $data['price'] * 100;
        $vnp_Locale = "VN";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'package_id' => $data['package_id'],
            'amount' => $data['price'],
            'txn_ref' => $vnp_TxnRef,
            'payment_method' => 'VNPay',
            'status' => 'pending', // Đặt trạng thái ban đầu là pending
        ]);



        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }


        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        );
        return redirect()->away($vnp_Url);
    }

    // public function vnpay_return(Request $request)
    // {
    //     $vnp_HashSecret = "241ZOFQ46R88O0TWLIA3TC9U9YH1WEXK"; // Chuỗi bí mật
    //     $inputData = $request->all();

    //     $txn_ref = $request->input('vnp_TxnRef');
    //     $vnp_SecureHash = $request->input('vnp_SecureHash');

    //     // Kiểm tra giao dịch có tồn tại không
    //     $payment = Payment::where('txn_ref', $txn_ref)->first();
    //     if (!$payment) {
    //         return redirect('/landlord/subscription')->with('error', 'Transaction not found.');
    //     }

    //     // Xác thực chữ ký từ VNPay
    //     unset($inputData['vnp_SecureHash']);
    //     ksort($inputData);
    //     $hashData = urldecode(http_build_query($inputData));
    //     $secureHashCheck = hash_hmac('sha512', $hashData, $vnp_HashSecret);

    //     if ($vnp_SecureHash !== $secureHashCheck) {
    //         return redirect('/landlord/subscription')->with('error', 'Invalid signature.');
    //     }

    //     // Cập nhật trạng thái thanh toán
    //     $status = $request->input('vnp_ResponseCode') == "00" ? 'completed' : 'failed';
    //     $payment->update(['status' => $status]);

    //     // Chuyển hướng với thông báo phù hợp
    //     if ($status == 'completed') {
    //         return redirect('/landlord')->with('success', 'Payment successful!');
    //     } else {
    //         return redirect('/landlord/subscription')->with('error', 'Payment failed.');
    //     }
    // }
    public function vnpay_return(Request $request)
    {
        $vnp_HashSecret = "241ZOFQ46R88O0TWLIA3TC9U9YH1WEXK"; // Chuỗi bí mật
        $inputData = $request->all();

        $txn_ref = $request->input('vnp_TxnRef');
        $vnp_SecureHash = $request->input('vnp_SecureHash');

        // Kiểm tra giao dịch có tồn tại không
        $payment = Payment::where('txn_ref', $txn_ref)->first();
        if (!$payment) {
            return redirect('/landlord/subscription')->with('error', 'Transaction not found.');
        }

        // Xác thực chữ ký từ VNPay
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = "";
        foreach ($inputData as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $hashData .= urlencode($key) . "=" . urlencode($value) . "&";
            }
        }
        $hashData = rtrim($hashData, "&");

        // Log dữ liệu để debug
        Log::info('Input Data: ', $inputData);
        Log::info('Hash Data: ', [$hashData]);

        $secureHashCheck = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Log chữ ký để debug
        Log::info('Secure Hash Check: ', [$secureHashCheck]);
        Log::info('VNPay Secure Hash: ', [$vnp_SecureHash]);

        if ($vnp_SecureHash !== $secureHashCheck) {
            return redirect('/landlord/subscription')->with('error', 'Invalid signature.');
        }

        // Cập nhật trạng thái thanh toán
        $status = $request->input('vnp_ResponseCode') == "00" ? 'completed' : 'failed';
        $payment->update(['status' => $status]);

        // Chuyển hướng với thông báo phù hợp
        if ($status == 'completed') {
            // Lấy thông tin gói subscription từ payment
            $package = SubscriptionPackage::find($payment->package_id);
            if (!$package) {
                return redirect('/landlord/subscription')->with('error', 'Package not found.');
            }
            Log::info('Package Post Limit: ', ['post_limit' => $package->post_limit]);
            // Tính toán ngày bắt đầu và ngày kết thúc
            $startDate = now();
            $endDate = now()->addDays($package->duration_days);

            Log::info('Creating subscription with remaining_posts: ', ['remaining_posts' => $package->post_limit]);

            // Tạo subscription với remaining_posts từ package
            Subscription::create([
                'user_id' => $payment->user_id,
                'package_id' => $payment->package_id,
                'payment_id' => $payment->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'remaining_posts' => $package->post_limit, // Cung cấp giá trị remaining_posts
                'status' => 'active',
            ]);

            return redirect('/landlord')->with('success', 'Payment successful!');
        } else {
            return redirect('/landlord/subscription')->with('error', 'Payment failed.');
        }
    }
}
