<?PHP

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\HomeService;

class homeApiCtlr extends Controller
{
    protected $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }
    public function tohome(Request $request)
    {
        $grid = $request->query('grid', '4'); // 預設為 4 列
        $sort = $request->query('sort', null); // 排序參數
        $data = $this->homeService->getHomeData($sort);
        
        return view('welcome', [
            'user' => $data['user'],
            'marqee' => $data['marqee'],
            'few_products' => $data['few_products'],
            'products_category' => $data['products_category'],
            'allProducts' => $data['allProducts'],
            'infos' => $data['infos'],
            'gride' => $grid
        ]);
    }
    public function toHome_with_search($search, Request $request)
    {
        $grid = $request->query('grid', '4'); // 預設為 4 列
        $sort = $request->query('sort', null); // 排序參數
        $data = $this->homeService->getHomeDataWithCategorySearch($search, $sort);
        
        return view('welcome', [
            'user' => $data['user'],
            'marqee' => $data['marqee'],
            'few_products' => $data['few_products'],
            'products_category' => $data['products_category'],
            'allProducts' => $data['allProducts'],
            'infos' => $data['infos'],
            'gride' => $grid
        ]);
    }
    public function toHome_words_search(Request $request)
    {
        $grid = $request->input('grid', '4'); // 預設為 4 列
        $sort = $request->query('sort', null); // 排序參數
        $result = $this->homeService->searchProductsByWords($request, $sort);
        
        if (isset($result['error'])) {
            return redirect()->route($result['redirect'])->with('error', $result['error']);
        }
        
        return view('welcome', [
            'user' => $result['user'],
            'marqee' => $result['marqee'],
            'few_products' => $result['few_products'],
            'products_category' => $result['products_category'],
            'allProducts' => $result['allProducts'],
            'infos' => $result['infos'],
            'gride' => $grid
        ]);
    }

    public function toItemPage($id)
    {
        $data = $this->homeService->getItemPageData($id);
        
        return view('itemPage', [
            'user' => $data['user'],
            'products' => $data['products'],
            'marqee' => $data['marqee'],
            'few_products' => $data['few_products']
        ]);
    }
}