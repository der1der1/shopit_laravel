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
    public function tohome()
    {
        $data = $this->homeService->getHomeData();
        
        return view('welcome', [
            'user' => $data['user'],
            'marqee' => $data['marqee'],
            'few_products' => $data['few_products'],
            'products_category' => $data['products_category'],
            'allProducts' => $data['allProducts'],
            'infos' => $data['infos']
        ]);
    }
    public function toHome_with_search($search)
    {
        $data = $this->homeService->getHomeDataWithCategorySearch($search);
        
        return view('welcome', [
            'user' => $data['user'],
            'marqee' => $data['marqee'],
            'few_products' => $data['few_products'],
            'products_category' => $data['products_category'],
            'allProducts' => $data['allProducts'],
            'infos' => $data['infos']
        ]);
    }
    public function toHome_words_search(Request $request)
    {
        $result = $this->homeService->searchProductsByWords($request);
        
        if (isset($result['error'])) {
            return redirect()->route($result['redirect'])->with('error', $result['error']);
        }
        
        return view('welcome', [
            'user' => $result['user'],
            'marqee' => $result['marqee'],
            'few_products' => $result['few_products'],
            'products_category' => $result['products_category'],
            'allProducts' => $result['allProducts'],
            'infos' => $result['infos']
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