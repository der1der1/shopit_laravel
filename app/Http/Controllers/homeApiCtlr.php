<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use Illuminate\Http\Request;

class homeApiCtlr extends Controller
{
    protected $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function tohome()
    {
        $data = $this->homeService->getHomePageData();
        return view('welcome', $data);
    }

    public function toHome_with_search($search)
    {
        $data = $this->homeService->getSearchPageData($search);
        
        if ($data === false) {
            return redirect()->route('home')
                ->with('error', '找不到您搜尋的項目，將為您返回。');
        }

        return view('welcome', $data);
    }

    public function toHome_words_search(Request $search)
    {
        $data = $this->homeService->getSearchPageData($search->search_word);
        
        if ($data === false) {
            return redirect()->route('home')
                ->with('error', '找不到您搜尋的項目，將為您返回。');
        }

        return view('welcome', $data);
    }

    public function toItemPage($id)
    {
        $data = $this->homeService->getItemPageData($id);
        return view('itemPage', $data);
    }
}