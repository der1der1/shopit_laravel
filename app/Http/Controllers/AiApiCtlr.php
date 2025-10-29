<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\marqeeModel;
use App\Models\productsModel;


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

        $products_category = productsModel::select('category')->distinct()->get();
        $categories = [];
        foreach ($products_category as $item) {
            $categories[] = $item->category;
        }
        $the_categories = implode(", ", $categories);
        
        $std_prompt = "
        目前您所提供的服務是以API方式掛載在一個購物網站上，以下是顧客所輸入的內容: '". $input ."'。
        請根據顧客的語句分析出他所需要的商品，並根據本網站有的商品分類，推薦適合的商品分類給顧客。
        以下是本網站的商品分類:'". $the_categories ."'。
        請回應顧客購物建議、使用建議、以及推薦產品分類的連結網址，並且以繁體中文回應，格式為純文字，謝謝！
        以下是本網站的商品分類連結: <a href=\"https://desmoco.com.tw/[這邊是您推薦的分類]\" target=\"_blank\">[這邊是您推薦的分類]</a>。
        提醒您，給商品分類的時候請不要加上括號'[]'這個符號；[這邊是您推薦的分類]請使用我給你得網站裡面有的分類。
        建議可以如以下:
        ...前文省略，以下建議您參考的產品:
        <a href=\"https://desmoco.com.tw/[這邊是您推薦的分類]\" target=\"_blank\">[這邊是您推薦的分類]</a>
        <a href=\"https://desmoco.com.tw/[這邊是您推薦的分類]\" target=\"_blank\">[這邊是您推薦的分類]</a>
        ...。
        ";

        $user = Auth::user();
        $marqee = marqeeModel::getAllMarqee();
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4o-mini',
            'input' => $std_prompt,
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
