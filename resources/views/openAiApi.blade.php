<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAI API Response</title>
</head>
<body>
    <form action="{{ route('testApi_request') }}" method="POST">
        @csrf
        <input type="text" name="query" placeholder="Ask me anything...">
        <button type="submit">Send</button>
    </form>

    @if (isset($input) && isset($output))
        <h2>結果</h2>
        <p><strong>輸入文字：</strong> {{ $input }}</p>
        <p><strong>回應文字：</strong> {{ $output }}</p>
    @endif
</body>
</html>