<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use App\Services\ProductService;
use App\Services\MarqueeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiApiCtlr extends Controller
{
    protected $aiService;
    protected $productService;
    protected $marqueeService;

    public function __construct(
        AiService $aiService,
        ProductService $productService,
        MarqueeService $marqueeService
    ) {
        $this->aiService = $aiService;
        $this->productService = $productService;
        $this->marqueeService = $marqueeService;
    }
    
    public function testApi_show(Request $request)
    {
        try {
            if (!$this->aiService->isApiKeyConfigured()) {
                return response()->json(['error' => 'OpenAI API key not configured'], 500);
            }

            $user = Auth::user();
            $marqee = $this->marqueeService->getAllMarquees();

            return view('openAiApi', compact('user', 'marqee'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function testApi_request(Request $request)
    {
        try {
            $input = $request->input('query');
            $user = Auth::user();
            $marqee = $this->marqueeService->getAllMarquees();

            $output = $this->aiService->generateProductRecommendations($input);

            return view('openAiApi', [
                'input' => $input,
                'output' => $output,
                'user' => $user,
                'marqee' => $marqee,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
