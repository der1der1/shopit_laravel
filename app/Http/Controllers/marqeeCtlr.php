<?php

namespace App\Http\Controllers;

use App\Services\MarqueeService;

class marqeeCtlr extends Controller
{
    protected $marqueeService;

    public function __construct(MarqueeService $marqueeService)
    {
        $this->marqueeService = $marqueeService;
    }

    public function marqee()
    {
        $marqee = $this->marqueeService->getAllMarquees();
        
        return view('welcome', compact('marqee'));
    }
}
