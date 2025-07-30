<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\marqeeModel;

class AiApiCtlr extends Controller
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }
    
    public function testApi_show(Request $request)
    {

        $user = Auth::user();
        $marqee = marqeeModel::getAllMarqee();

        if (!$this->apiKey) {
            return response()->json(['error' => 'OpenAI API key not configured'], 500);
        }

        

        return view('openAiApi', compact('user', 'marqee'));

    }

    public function testApi_request(Request $request)
    {
        $input = $request->input('query');
        
        $user = Auth::user();
        $marqee = marqeeModel::getAllMarqee();
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4o-mini',
            'input' => $input,
        ]);

        // 提取 API 回應內容
        $responseData = $response->json();
        $output = $responseData['output'][0]['content'][0]['text'] ?? 'No response from OpenAI';

        // 將回應傳遞給 Blade 模板
        return view('openAiApi', [
            'input' => $input,
            'output' => $output,
            'user' => $user,
            'marqee' => $marqee,
        ]);
    }
}
