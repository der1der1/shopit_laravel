<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <!-- 將 CSS 文件連結到 HTML -->
    <link rel="stylesheet" href="{{ asset('login.css') }}">
    <!-- 將 JS  文件連結到 HTML -->
</head>

<body>
    <header id="title">
        <h1>Account Verification</h1>
    </header>

    <div id="backsback">
        <div id="back_verification">
            <div id="login_words">
                <h2 id="login_words_h2">Verification Code</h2>
                <h3 id="login_words_h3">
                    Thanks fot sign up, we have sent the verification code to your email.
                    {{ $user->email }}
                </h3>
                <form method="POST" action="{{ route('verification_check') }}">
                    @csrf
                    <div id="remember_me_box">
                        <input type="text" id="verification_code_input" name="verification_code" placeholder="Enter verification code">
                        <div>
                            <input type="checkbox" id="remember_me" name="remember_me">
                            <label for="remember_me">記住我</label>
                        </div>
                    </div>

                    <button id="goto_signup" type="submit">Verify !</button>
                    <input hidden type="text" value="{{ $user->email }}" name="email">
                </form>
                <form method="POST" action="{{ route('verification_resend') }}">
                    @csrf
                    <button id="goto_signup" type="submit" title="recieved nothing?">resend mail</button>
                    <input hidden type="text" value="{{ $user->email }}" name="email">
                </form>
            </div>
        </div>
    </div>

    <!-- 顯示錯誤訊息 -->
    @if ($errors->has('msg'))
    <script>
        alert("{{ $errors->first('msg') }}");
    </script>
    @endif
</body>
@include('template.footer_template')

</html>