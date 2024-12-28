<?PHP

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\contactModel;
use Illuminate\Http\Request;

class contactCtlr extends Controller
{
    public function reporting(Request $request)
    {
        try {
            // $theuser = !empty(Auth::user()) ?: $request->name ;

            $user = contactModel::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'information' => $request->information,
            ]);

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
