<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiApiCtlr extends Controller
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }
    
    public function testApi_show(Request $request)
    {
        // Example of using OpenAI API
        if (!$this->apiKey) {
            return response()->json(['error' => 'OpenAI API key not configured'], 500);
        }

        
        return view('openAiApi', [
            'message' => 'OpenAI API is ready to use.'
        ]);
    }

    public function testApi_request(Request $request)
    {
        $input = $request->input('query');
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'o4-mini',
            'input' => $input,
            'max_tokens' => 150,
        ]);

        // 提取 API 回應內容
        $responseData = $response->json();
        $output = $responseData['choices'][0]['text'] ?? 'No response from OpenAI';

        // 將回應傳遞給 Blade 模板
        return view('openAiApi', [
            'input' => $input,
            'output' => $output,
        ]);
    }
}
