<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\ProductService;

class AiService
{
    protected $apiKey;
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->productService = $productService;
    }

    public function generateProductRecommendations(string $userInput)
    {
        if (!$this->apiKey) {
            throw new \Exception('OpenAI API key not configured');
        }

        $categories = $this->productService->getAllCategories()
            ->pluck('category')
            ->implode(', ');

        $prompt = "I'm the administrator of the shopping website, " .
                 "plz provide my customer some product based on the following categories: " .
                 $categories . 
                 ", if needed, plz prvide some link for my categories of product, " .
                 "https://desmoco.com.tw/[here is the category you suggested] " .
                 ".And the demands post by customer are below: " . $userInput;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.openai.com/v1/responses', [
                'model' => 'gpt-4o-mini',
                'input' => $prompt,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to get response from OpenAI');
            }

            $responseData = $response->json();
            return $responseData['output'][0]['content'][0]['text'] ?? 'No response from OpenAI';

        } catch (\Exception $e) {
            throw new \Exception('AI Service Error: ' . $e->getMessage());
        }
    }

    public function isApiKeyConfigured()
    {
        return !empty($this->apiKey);
    }
}