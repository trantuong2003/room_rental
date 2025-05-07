<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Email Confirmation</title>
    <link rel="stylesheet" href="{{ asset('assets/css/account/verify.css') }}">
</head>

<body>
    <div class="container">
        <h1>Please check your email to confirm your account.</h1>

        @if (session('message'))
        <p class="success-message">{{ session('message') }}</p>
        @endif

        <p>If you do not receive the email, click the button below to resend.</p>

        <form id="resendEmailForm" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn">Resend confirmation email</button>
        </form>
    </div>

    <script>
        document.getElementById("resendEmailForm").addEventListener("submit", function (event) {
            event.preventDefault();

            fetch(this.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || "Confirmation email has been resent!");
            })
            .catch(error => {
                console.error("Error resending email:", error);
                alert("The confirmation email could not be resent.");
            });
        });
    </script>
</body>

</html>