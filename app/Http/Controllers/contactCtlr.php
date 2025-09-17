<?PHP

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Repository\ContactRepository;
use Illuminate\Http\Request;

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
}
