<?PHP

namespace App\Http\Controllers;

// use App\Models\selectUserModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use App\Models\marqeeModel;
use App\Models\productsModel;

use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;

class homeApiCtlr extends Controller
{
    public function tohome()
    {
        // 使用模型方法來獲取所有model
        // $user = selectUserModel::all();
        $user = Auth::user();
        $marqee = marqeeModel::getAllMarqee();
        $allProducts = productsModel::all();
        $few_products = productsModel::limit(5)->get();
        $products_category = productsModel::select('category')->distinct()->get();

        $infos = array();
        if ($user) {
           $info = explode(';',$user->info0);
            foreach ($info as $infoea) {
                $infos[] = $infoea;
            } 
        } 
        
        // 返回結果，傳遞給 welcome.blade.php 視圖
        return view('welcome', compact('user', 'marqee', 'few_products', 'products_category','allProducts', 'infos'));
        // return view('welcome', compact( 'marqee', 'few_products', 'products_category','allProducts'));

    }
    public function toHome_with_search($search)
    {
        // 使用模型方法來獲取所有modelgetNameCatgrySearch
        // $user = selectUserModel::all();
        $user = Auth::user();

        $marqee = marqeeModel::getAllMarqee();
        $few_products = productsModel::limit(5)->get();
        $products_category = productsModel::select('category')->distinct()->get();
        // $search = strval($search);
        $allProducts = productsModel::where('category', $search)->get();
        // dump($products);
        // dump($products_category);
        // 返回結果，傳遞給 welcome.blade.php 視圖
        return view('welcome', compact('user', 'marqee', 'few_products', 'products_category', 'allProducts'));
        // return view('welcome', compact( 'marqee', 'few_products', 'products_category', 'allProducts'));

    }
    public function toHome_words_search(Request $search)
    {
        try {
            // 其他應回傳
            // $user = selectUserModel::all();
            $user = Auth::user();

            $marqee = marqeeModel::getAllMarqee();
            $few_products = productsModel::limit(5)->get();
            $products_category = productsModel::select('category')->distinct()->get();

            $allProducts = productsModel::where('category', $search->search_word)->get();
            $allProducts = $allProducts->isEmpty() ? productsModel::all() : $allProducts;
            return view('welcome', compact('user', 'marqee', 'few_products', 'products_category', 'allProducts'));
            // return view('welcome', compact('marqee', 'few_products', 'products_category', 'allProducts'));

        } catch (\Exception $e) {
            // 記錄完整錯誤信息
            return back()->with('error', '搜尋發生錯誤');
        }

    }

    public function toItemPage($id)
    {
        $user = Auth::user();
        $marqee = marqeeModel::getAllMarqee();
        $products = productsModel::where('id', $id)->first();
        $few_products = productsModel::inRandomOrder()->limit(4)->get(); // 取得 #指定個數的隨機資料
        return view('itemPage', compact('user', 'products', 'marqee', 'few_products'));
    }
}
