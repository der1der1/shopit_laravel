<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\ContactRequest;
use App\Services\ContactService;
use Illuminate\Support\Facades\Auth;

class contactCtlr extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function reporting(ContactRequest $request)
    {
        try {
            $this->contactService->createContact($request->validated());
            return redirect()->route('home')->with('success', '送出成功');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function report_show()
    {
        $user = Auth::user();
        return view('contact', compact('user'));
    }
}
