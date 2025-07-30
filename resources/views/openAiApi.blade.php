<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAI API Response</title>

    @include('template.head_template')

    <style>
        body {
            background-color: #DEB887;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        header {
            padding-bottom: 20px;
        }
        #search {
            height: 20px;
        }
        #tool {
            padding-top: 0;
        }
        .container {
            max-width: 800px;
            margin: 100px auto;
            background-color: #FFF8DC;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: 2px solid #D2B48C;
        }
        h1 {
            text-align: center;
            color: #8B4513;
            margin-bottom: 30px;
        }
        .form-container {
            background-color: #FFF8DC;
            border: 2px solid #D2B48C;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 2px solid #D2B48C;
            border-radius: 5px;
            font-size: 16px;
            background-color: #FFFEF7;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #8B4513;
        }
        button {
            padding: 12px 20px;
            background-color: #8B4513;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #A0522D;
        }
        .chat-container {
            background-color: #FFF8DC;
            border: 2px solid #D2B48C;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .message {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #D2B48C;
        }
        .user-message {
            background-color: #F5E6D3;
            border-left: 4px solid #8B4513;
        }
        .ai-message {
            background-color: #E6F3FF;
            border-left: 4px solid #4169E1;
        }
        .message-label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #8B4513;
        }
        .message-content {
            line-height: 1.6;
            white-space: pre-wrap;
        }
        
        #chatbot_img {
            height: 50px;
            width: 50px;
        }
        
    </style>
    
</head>
<body>
    @include('template.header_template')
    <div class="container">


        <h1>
            <img id="chatbot_img" src="{{ asset('img/icon/chatbot.png') }}" alt="AI chatta!">
            AI 對話助手
        </h1>
        
        <div class="form-container">
            <form action="{{ route('testApi_request') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="query" placeholder="在此輸入您的問題..." value="" required>
                    <button type="submit">發送</button>
                </div>
            </form>
        </div>

        @if (isset($input) && isset($output))
            <div class="chat-container">
                <h2 style="color: #8B4513; margin-bottom: 20px;">我的回應</h2>
                
                <div class="message user-message">
                    <div class="message-label">👤 您的問題：</div>
                    <div class="message-content">{{ $input }}</div>
                </div>
                
                <div class="message ai-message">
                    <div class="message-label">
                        <img  src="{{ asset('img/icon/chatbot.png') }}" alt="AI chatta!" style="width: 15px; height: 15px;">
                        AI 回應：
                    </div>
                    <div class="message-content">{{ $output }}</div>
                </div>
            </div>
        @endif
    </div>

    @include('template.footer_template')

</body>
</html>