<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Renter</title>
  <link rel="stylesheet" href="{{ asset('assets/css/customer.css') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <script src="{{ asset('assets/js/customer.js') }}"></script>
</head>

<body>
  @if (session('msg'))
  <div class="">
    {{session('msg')}}
  </div>
  @endif
  <header>
    <div class="navbar">
      <div class="navbar-left">
        <img alt="Batdongsan logo" height="40" src="assets/image/logotro.png" width="100" />
        <a href="/">
          Trang chủ
        </a>
        <a class="active" href="messagecustomer">
          Tin nhắn của tôi
        </a>
        <a href="#">
          Dự án
        </a>
        <a href="#">
          Tin tức
        </a>
        <a href="#">
          Wiki BĐS
        </a>
        <a href="#">
          Phân tích đánh giá
        </a>
        <a href="#">
          Danh bạ
        </a>
      </div>
      <div class="navbar-right">
        <a href="/favourite">
          Yêu thích
        </a>
        <i class="fas fa-heart icon">
        </i>
        <a href="/login">
          Đăng nhập
        </a>
        <a href="/register">
          Đăng ký
        </a>
        <div class="button">
          <a href="/postcustomer">Đăng tin</a>
        </div>
        <div>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="button" type="submit" class="btn btn-danger">Logout</button>
          </form>
        </div>


      </div>
    </div>
  </header>
  <main>
    @yield('content')
  </main>

  <footer class="py-8">
    <div class="container">
      <div class="grid">
        <div>
          <h2>Về chúng tôi</h2>
          <p>Website cho thuê phòng trọ, nhà trọ nhanh chóng và hiệu quả</p>
          <p class="mt-4"><i class="fas fa-map-marker-alt"></i> 32-34 Điện Biên Phủ, Đakao, Quận 1, TP.HCM</p>
          <p class="mt-2"><i class="fas fa-phone"></i> 0938.346.873</p>
          <p class="mt-2"><i class="fas fa-envelope"></i> nhatnoviet@gmail.com</p>
        </div>
        <div>
          <h2>Giới thiệu</h2>
          <ul>
            <li>Giới thiệu</li>
            <li>Quy chế hoạt động</li>
            <li>Chính sách bảo mật</li>
            <li>Quy định sử dụng</li>
            <li>Liên hệ</li>
          </ul>
        </div>
        <div>
          <h2>Hỗ trợ</h2>
          <ul>
            <li>Bảng giá dịch vụ</li>
            <li>Hướng dẫn đăng tin</li>
            <li>Quy định đăng tin</li>
            <li>Cơ chế giải quyết tranh chấp</li>
            <li>Tin tức</li>
          </ul>
        </div>
        <div>
          <h2>Phương thức thanh toán</h2>
          <div class="payment-methods">
            <img src="https://storage.googleapis.com/a1aa/image/LQ252yRYEd5CXTWMZI4UHrGUhbdx7D70RU8DM9KSpUs.jpg"
              alt="Visa logo">
            <img src="https://storage.googleapis.com/a1aa/image/a4b_r0k7C4_FZ8Em7L9S7JgfRUpVs9SQmgubmTrQSkc.jpg"
              alt="Mastercard logo">
            <img src="https://storage.googleapis.com/a1aa/image/8Lhzh1_sRzD0RdLYZCiIsceItRjaCztcspeMeCNOwVo.jpg"
              alt="Internet Banking logo">
            <img src="https://storage.googleapis.com/a1aa/image/rl1kEpsRxPmp1twH19nKCpVnMKDOW_5kyxUNpUuERAA.jpg"
              alt="MoMo logo">
            <img src="https://storage.googleapis.com/a1aa/image/0cmfONJbaY_XRcOOhfCzlnJWkv4mbtNwoCf6K5ql8yI.jpg"
              alt="CQT Tiền Nhật logo">
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>Copyright © 2011 - 2024 | thuephongtro.com. Ghi rõ nguồn "thuephongtro.com" khi phát hành lại thông tin từ
          website này.</p>
      </div>
    </div>
  </footer>

</body>

</html>