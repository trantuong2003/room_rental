<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Xác nhận Email</title>
    <link rel="stylesheet" href="{{ asset('assets/css/account/verify.css') }}">
   
</head>

<body>

    <div class="container">
        <h1>Vui lòng kiểm tra email để xác nhận tài khoản của bạn.</h1>

        @if (session('message'))
        <p class="success-message">{{ session('message') }}</p>
        @endif

        <p>Nếu bạn không nhận được email, hãy nhấn vào nút bên dưới để gửi lại.</p>

        <form id="resendEmailForm" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn">Gửi lại email xác nhận</button>
        </form>
    </div>

    <script>
        document.getElementById("resendEmailForm").addEventListener("submit", function (event) {
        event.preventDefault();

        fetch(this.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json" // Yêu cầu server trả về JSON
            },
        })
        .then(response => response.json()) 
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            console.error("Lỗi khi gửi lại email:", error);
            alert("Không thể gửi lại email xác nhận.");
        });
    });
    </script>

</body>

</html>