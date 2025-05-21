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
        $few_products = productsModel::inRandomOrder()->limit(5)->get();
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
        $few_products = productsModel::inRandomOrder()->limit(5)->get();
        $products_category = productsModel::select('category')->distinct()->get();
        // $search = strval($search);
        $allProducts = productsModel::where('category', $search)->get();
        // dump($products);
        // dump($products_category);
        $infos = array();
        if ($user) {
           $info = explode(';',$user->info0);
            foreach ($info as $infoea) {
                $infos[] = $infoea;
            } 
        } 
        // 返回結果，傳遞給 welcome.blade.php 視圖
        return view('welcome', compact('user', 'marqee', 'few_products', 'products_category', 'allProducts', 'infos'));
        // return view('welcome', compact( 'marqee', 'few_products', 'products_category', 'allProducts'));

    }
    public function toHome_words_search(Request $search)
    {
        // 先查詢
        $allProducts = productsModel::where('category', $search->search_word)->get();
        if ($allProducts->isEmpty()) {
            // 若無，報錯返回
            return redirect()->route('home')->with('error', '找不到您搜尋的項目，將為您返回。');
        } else {
            // 若有，正常傳遞
            $user = Auth::user();

            $marqee = marqeeModel::getAllMarqee();
            $few_products = productsModel::inRandomOrder()->limit(5)->get();
            $products_category = productsModel::select('category')->distinct()->get();

            $infos = array();
            if ($user) {
               $info = explode(';',$user->info0);
                foreach ($info as $infoea) {
                    $infos[] = $infoea;
                } 
            } 
            return view('welcome', compact('user', 'marqee', 'few_products', 'products_category', 'allProducts', 'infos'));
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