<?PHP

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Repository\ContactRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class contactCtlr extends Controller
{
    protected $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }
    public function reporting(Request $request)
    {
        try {
            $contactData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'information' => $request->information,
            ];

            $this->contactRepository->createContactReport($contactData);

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

    /**
     * 回覆聯絡訊息並寄送 Email
     */
    public function reply(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'id' => 'required|integer',
            'message' => 'required|string',
        ]);

        try {
            $to = $request->email;
            $replyMessage = $request->message;
            $contactId = $request->id;

            $subject = config('mail.from.name') . ' 聯絡訊息回覆';

            // 使用 Mail::raw 寄送純文字/HTML email
            Mail::raw($replyMessage, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });

            // 更新資料庫，標記為已回覆
            $this->contactRepository->Replied($contactId);

            Log::info('聯絡訊息回覆已寄出', ['to' => $to, 'contact_id' => $contactId]);
            return response()->json(['success' => true, 'message' => '回覆已寄出']);
        } catch (\Exception $e) {
            Log::error('聯絡訊息回覆寄送失敗', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => '寄送失敗：' . $e->getMessage()], 500);
        }
    }
}
