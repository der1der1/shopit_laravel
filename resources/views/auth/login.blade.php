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
        <h1>Log in, for the best purchase quality.</h1>
    </header>

    <div id="backsback">
        <div id="back">
            <div id="login_words">
                <h2 id="login_words_h2">Log In</h2>
                <h3 id="login_words_h3">Log in, if you had an account. Perchasing with account, you would enjoy a joyful experience.</h3>
                <button id="goto_signup" onclick="goto_signup()">go to Sign Up</button>
            </div>
            <div id="sighup_words">
                <h2 id="sighup_words_h2">Sign Up (admin./)</h2>
                <h3 id="sighup_words_h3">If you don't have account, just join us. A brilliant decision always keep people be with wisdom.</h3>
                <button id="goto_login" onclick="goto_login()">go to Log In</button>
            </div>

            <!-- 跳動面板由於CSS設定需以id="back"為根據，故須放置在其<div>下 -->

            <div id="panel" class="panel_log">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div id="choser_login">
                        <h2 id="panel_login" class="panel_login">Log In</h2>
                        <input type="email" id="account_input" name="account" placeholder="e-mail">
                        <input type="password" id="password_input" name="password" placeholder="password">
                        <input type="checkbox" id="remember" name="remember"> Remember Me
                        <div id="choice">
                            <input type="submit" id="choice_login" name="login" value="Log In" class="choice_login_signup">
                        </div>
                        <div class="cf-turnstile" data-sitekey="{{ env('CLOUDFLARE_TURNSTILE_SITE_KEY') }}"data-callback="javascriptCallback"  style="margin: 0 0 0 10px;"></div>
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback" defer></script>
                    </div>
                </form>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div id="choser_sign" class="none">
                        <h2 id="panel_signup" class="panel_login">Sign Up</h2>
                        <input type="text" id="name_input" name="name" placeholder="Name">
                        <input type="email" id="account_input" name="account" placeholder="e-mail" title="please provide a valid email for account activation.">
                        <input type="password" id="password_input" name="password" placeholder="password">
                        <div id="choice">
                            <input type="submit" id="choice_login" name="signup" value="Sign Up" class="choice_login_signup">
                        </div>
                        <div class="cf-turnstile" data-sitekey="{{ env('CLOUDFLARE_TURNSTILE_SITE_KEY') }}"data-callback="javascriptCallback" style="margin: -25px 0 0 10px;"></div>
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback" defer></script>
                    </div>
                </form>
            </div>

            <script>
                function goto_login() {
                    document.getElementById("panel").className = "panel_log";
                    document.getElementById("choser_login").className = 'show';
                    document.getElementById("choser_sign").className = "none";

                }
            
                function goto_signup() {
                    document.getElementById("panel").className = "panel_sign";
                    document.getElementById("choser_login").className = 'none';
                    document.getElementById("choser_sign").className = "show";
                }
            </script>

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

