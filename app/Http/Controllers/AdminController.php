<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\productsModel;
use App\Models\purchasedModel;
use App\Models\contactModel;
use App\Models\mailListModel;
use App\Models\marqeeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Constructor - 驗證管理員權限
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            
            if (!$user || $user->prvilige != 'A') {
                return redirect()->route('home')->with('error', '您沒有權限執行此操作');
            }
            
            return $next($request);
        });
    }
    
    /**
     * Display the admin dashboard with statistics
     */
    public function dashboard()
    {
        // Calculate statistics
        $stats = [
            'total_users' => User::count(),
            'total_products' => productsModel::count(),
            'total_orders' => purchasedModel::count(),
            'today_orders' => purchasedModel::whereDate('created_at', today())->count(),
            'pending_contacts' => contactModel::count(),
            'mail_subscribers' => mailListModel::where('onoff', 1)->count(),
            
            // Order statistics
            'paid_orders' => purchasedModel::where('payed', '1')->count(),
            'delivered_orders' => purchasedModel::where('delivered', '1')->count(),
            'pending_orders' => purchasedModel::where('payed', '0')->count(),
            
            // Products by category
            'products_by_category' => productsModel::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
            
            // Recent orders
            'recent_orders' => purchasedModel::orderBy('created_at', 'desc')->take(5)->get(),
            
            // Recent contacts
            'recent_contacts' => contactModel::orderBy('created_at', 'desc')->take(5)->get(),
        ];
        
        return view('admin', compact('stats'));
    }
    
    /**
     * Display users list
     */
    public function users()
    {
        $users = User::orderBy('prvilige', 'asc')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }
    
    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin.users-create');
    }
    
    /**
     * Store a new user
     */
    public function storeUser(Request $request)
    {
        try {
            $request->validate([
                'account' => 'required|unique:users,account',
                'password' => 'required|min:6',
                'prvilige' => 'required'
            ]);
            
            User::create([
                'account' => $request->account,
                'password' => bcrypt($request->password),
                'name' => $request->name,
                'prvilige' => $request->prvilige,
                'email' => $request->email,
                'phone' => $request->phone,
                'nickname' => $request->nickname,
                'to_address' => $request->to_address,
                'to_shop' => $request->to_shop,
                'bank_account' => $request->bank_account,
            ]);
            
            return redirect()->route('admin.users')->with('success', '用戶已新增');
        } catch (\Exception $e) {
            return back()->with('error', '新增失敗：' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Show edit user form
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users-edit', compact('user'));
    }
    
    /**
     * Update a user
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $user->account = $request->account;
            $user->name = $request->name;
            $user->prvilige = $request->prvilige;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->nickname = $request->nickname;
            $user->to_address = $request->to_address;
            $user->to_shop = $request->to_shop;
            $user->bank_account = $request->bank_account;
            
            // 如果有新密碼才更新
            if ($request->password) {
                $user->password = bcrypt($request->password);
            }
            
            $user->save();
            
            return redirect()->route('admin.users')->with('success', '用戶已更新');
        } catch (\Exception $e) {
            return back()->with('error', '更新失敗：' . $e->getMessage());
        }
    }
    
    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['success' => true, 'message' => '用戶已刪除']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Display products list
     */
    public function products()
    {
        $products = productsModel::orderBy('created_at', 'desc')->paginate(20);
        $categories = productsModel::distinct()->pluck('category');
        
        return view('admin.products', compact('products', 'categories'));
    }
    
    /**
     * Show create product form
     */
    public function createProduct()
    {
        return view('admin.products-create');
    }
    
    /**
     * Store a new product
     */
    public function storeProduct(Request $request)
    {
        try {
            $request->validate([
                'product_name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'category' => 'required',
                'pic_dir' => 'required|image'
            ]);
            
            // Handle image upload
            $imagePath = 'img/pictureTarget/default.png';
            if ($request->hasFile('pic_dir')) {
                $image = $request->file('pic_dir');
                $image->move(public_path('img/pictureTarget'), $image->getClientOriginalName());
                $imagePath = 'img/pictureTarget/' . $image->getClientOriginalName();
            }
            
            productsModel::create([
                'product_name' => $request->product_name,
                'description' => $request->description,
                'price' => $request->price,
                'ori_price' => $request->ori_price ?? $request->price,
                'category' => $request->category,
                'pic_name' => $request->pic_name,
                'pic_dir' => $imagePath,
                'selected' => $request->selected ?? '0',
            ]);
            
            return redirect()->route('admin.products')->with('success', '商品已新增');
        } catch (\Exception $e) {
            return back()->with('error', '新增失敗：' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Show edit product form
     */
    public function editProduct($id)
    {
        $product = productsModel::findOrFail($id);
        return view('admin.products-edit', compact('product'));
    }
    
    /**
     * Update a product
     */
    public function updateProduct(Request $request, $id)
    {
        try {
            $product = productsModel::findOrFail($id);
            
            $product->product_name = $request->product_name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->ori_price = $request->ori_price ?? $request->price;
            $product->category = $request->category;
            $product->pic_name = $request->pic_name;
            $product->selected = $request->selected ?? '0';
            
            // Handle image upload if provided
            if ($request->hasFile('pic_dir')) {
                $image = $request->file('pic_dir');
                $image->move(public_path('img/pictureTarget'), $image->getClientOriginalName());
                $product->pic_dir = 'img/pictureTarget/' . $image->getClientOriginalName();
            }
            
            $product->save();
            
            return redirect()->route('admin.products')->with('success', '商品已更新');
        } catch (\Exception $e) {
            return back()->with('error', '更新失敗：' . $e->getMessage());
        }
    }
    
    /**
     * Delete a product
     */
    public function deleteProduct($id)
    {
        try {
            $product = productsModel::findOrFail($id);
            $product->delete();
            return response()->json(['success' => true, 'message' => '商品已刪除']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Upload image for TinyMCE editor
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|max:2048' // Max 2MB
            ]);
            
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $filename = time() . '_' . $image->getClientOriginalName();
                
                // Create directory if it doesn't exist
                $uploadPath = public_path('img/products/descriptions');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $image->move($uploadPath, $filename);
                $imageUrl = asset('img/products/descriptions/' . $filename);
                
                return response()->json(['location' => $imageUrl]);
            }
            
            return response()->json(['error' => 'No file uploaded'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Display orders list
     */
    public function orders()
    {
        $orders = purchasedModel::orderBy('created_at', 'desc')->paginate(20);
        // dump($orders);
        return view('admin.orders', compact('orders'));
    }
    
    /**
     * Get order details
     */
    public function getOrder($id)
    {
        try {
            $order = purchasedModel::findOrFail($id);
            return response()->json(['success' => true, 'order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
    
    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            $order = purchasedModel::findOrFail($id);
            
            $order->payed = $request->has('payed') ? '1' : '0';
            $order->delivered = $request->has('delivered') ? '1' : '0';
            $order->recieved = $request->has('recieved') ? '1' : '0';
            
            $order->save();
            
            return response()->json(['success' => true, 'message' => '訂單狀態已更新']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Delete an order
     */
    public function deleteOrder($id)
    {
        try {
            $order = purchasedModel::findOrFail($id);
            $order->delete();
            return response()->json(['success' => true, 'message' => '訂單已刪除']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Display contacts list
     */
    public function contacts()
    {
        // 分開未回覆和已回覆的訊息
        $pendingContacts = contactModel::where('replied', 0)->orderBy('created_at', 'desc')->get();
        $repliedContacts = contactModel::where('replied', 1)->orderBy('created_at', 'desc')->get();
        
        return view('admin.contacts', compact('pendingContacts', 'repliedContacts'));
    }
    
    /**
     * Get contact details
     */
    public function getContact($id)
    {
        try {
            $contact = contactModel::findOrFail($id);
            return response()->json(['success' => true, 'contact' => $contact]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
    
    /**
     * Delete a contact
     */
    public function deleteContact($id)
    {
        try {
            $contact = contactModel::findOrFail($id);
            $contact->delete();
            return response()->json(['success' => true, 'message' => '聯絡訊息已刪除']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Display mail list
     */
    public function maillist()
    {
        $mailList = mailListModel::orderBy('created_at', 'desc')->paginate(20);
        
        $stats = [
            'total' => mailListModel::count(),
            'active' => mailListModel::where('onoff', 1)->count(),
            'inactive' => mailListModel::where('onoff', 0)->count(),
        ];
        
        return view('admin.maillist', compact('mailList', 'stats'));
    }
    
    /**
     * Toggle mail list status
     */
    public function toggleMailStatus(Request $request, $id)
    {
        try {
            $mail = mailListModel::findOrFail($id);
            $mail->onoff = $request->input('status');
            $mail->save();
            
            return response()->json(['success' => true, 'message' => '狀態已更新']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Delete a mail list entry
     */
    public function deleteMail($id)
    {
        try {
            $mail = mailListModel::findOrFail($id);
            $mail->delete();
            return response()->json(['success' => true, 'message' => '訂閱已刪除']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Display marquee list
     */
    public function marquee()
    {
        $marquees = marqeeModel::orderBy('rank', 'asc')->orderBy('created_at', 'desc')->get();
        return view('admin.marquee', compact('marquees'));
    }
    
    /**
     * Store new marquee
     */
    public function storeMarquee(Request $request)
    {
        $request->validate([
            'texts' => 'required|string|max:200|unique:marqee,texts'
        ]);
        
        try {
            // Get the max rank and add 1
            $maxRank = marqeeModel::max('rank') ?? 0;
            
            marqeeModel::create([
                'texts' => $request->texts,
                'rank' => $maxRank + 1
            ]);
            
            return redirect()->route('admin.marquee')
                ->with('success', '跑馬燈訊息已新增');
        } catch (\Exception $e) {
            return redirect()->route('admin.marquee')
                ->with('error', '新增失敗：' . $e->getMessage());
        }
    }
    
    /**
     * Update marquee
     */
    public function updateMarquee(Request $request, $id)
    {
        $request->validate([
            'texts' => 'required|string|max:200'
        ]);
        
        try {
            $marquee = marqeeModel::findOrFail($id);
            $marquee->texts = $request->texts;
            $marquee->save();
            
            return redirect()->route('admin.marquee')
                ->with('success', '跑馬燈訊息已更新');
        } catch (\Exception $e) {
            return redirect()->route('admin.marquee')
                ->with('error', '更新失敗：' . $e->getMessage());
        }
    }
    
    /**
     * Update marquee rank
     */
    public function updateMarqueeOrder(Request $request)
    {
        try {
            $order = $request->input('order', []);
            
            foreach ($order as $index => $id) {
                marqeeModel::where('id', $id)->update(['rank' => $index + 1]);
            }
            
            return response()->json(['success' => true, 'message' => '順序已更新']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Delete marquee
     */
    public function destroyMarquee($id)
    {
        try {
            $marquee = marqeeModel::findOrFail($id);
            $marquee->delete();
            
            return redirect()->route('admin.marquee')
                ->with('success', '跑馬燈訊息已刪除');
        } catch (\Exception $e) {
            return redirect()->route('admin.marquee')
                ->with('error', '刪除失敗：' . $e->getMessage());
        }
    }
}
