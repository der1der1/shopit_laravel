<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\productsModel;
use App\Models\purchasedModel;
use App\Models\contactModel;
use App\Models\mailListModel;
use App\Models\marqeeModel;
use App\Models\PaymentMethodModel;
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
        $categories = productsModel::distinct()->pluck('category')->filter();
        return view('admin.products-create', compact('categories'));
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
                'images' => 'required|array|min:1|max:4',
                'images.*' => 'image|max:5120',
                'image_names' => 'required|array|min:1|max:4',
                'image_names.0' => 'required'
            ]);
            
            // Handle multiple images upload
            $imageNames = $request->input('image_names', []);
            $uploadedImages = [];
            
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    if ($image) {
                        $filename = time() . '_' . $index . '_' . $image->getClientOriginalName();
                        $image->move(public_path('img/pictureTarget'), $filename);
                        $uploadedImages[] = 'img/pictureTarget/' . $filename;
                    }
                }
            }
            
            // 第一張圖片作為預設圖片 (pic_name, pic_dir)
            $firstImagePath = $uploadedImages[0] ?? 'img/pictureTarget/default.png';
            $firstImageName = $imageNames[0] ?? null;
            
            // 其他圖片儲存在 pic_name_more 和 pic_dir_more
            $additionalImagePaths = array_slice($uploadedImages, 1);
            $additionalImageNames = array_slice($imageNames, 1);
            
            // 清理空值
            $additionalImageNames = array_filter($additionalImageNames, function($name) {
                return !empty($name);
            });
            
            productsModel::create([
                'product_name' => $request->product_name,
                'description' => $request->description,
                'price' => $request->price,
                'ori_price' => $request->ori_price ?? $request->price,
                'category' => $request->category,
                'pic_name' => $firstImageName,
                'pic_dir' => $firstImagePath,
                'pic_name_more' => !empty($additionalImageNames) ? json_encode(array_values($additionalImageNames)) : null,
                'pic_dir_more' => !empty($additionalImagePaths) ? json_encode($additionalImagePaths) : null,
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
        $categories = productsModel::distinct()->pluck('category')->filter();
        return view('admin.products-edit', compact('product', 'categories'));
    }
    
    /**
     * Update a product
     */
    public function updateProduct(Request $request, $id)
    {
        try {
            $product = productsModel::findOrFail($id);
            
            // 檢查是否有首圖（第一張圖片必須存在）
            $hasFirstImage = false;
            if ($request->hasFile('images.0')) {
                $hasFirstImage = true;
            } elseif ($request->has('existing_images.0') && !empty($request->input('existing_images.0'))) {
                $hasFirstImage = true;
            }
            
            if (!$hasFirstImage) {
                return back()->with('error', '請上傳首圖')->withInput();
            }
            
            $product->product_name = $request->product_name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->ori_price = $request->ori_price ?? $request->price;
            $product->category = $request->category;
            $product->selected = $request->selected ?? '0';
            
            // 取得現有圖片路徑
            $existingImages = $request->input('existing_images', []);
            $imageNames = $request->input('image_names', []);
            
            // 處理第一張圖片（首圖）
            if ($request->hasFile('images.0')) {
                // 上傳新的首圖
                $image = $request->file('images.0');
                $filename = time() . '_0_' . $image->getClientOriginalName();
                $image->move(public_path('img/pictureTarget'), $filename);
                $product->pic_dir = 'img/pictureTarget/' . $filename;
                $product->pic_name = $imageNames[0] ?? null;
            } else {
                // 保留現有首圖
                if (isset($existingImages[0]) && !empty($existingImages[0])) {
                    $product->pic_dir = $existingImages[0];
                    $product->pic_name = $imageNames[0] ?? $product->pic_name;
                }
            }
            
            // 處理第2-4張圖片
            $additionalImagePaths = [];
            $additionalImageNames = [];
            
            for ($i = 1; $i < 4; $i++) {
                if ($request->hasFile("images.{$i}")) {
                    // 上傳新圖片
                    $image = $request->file("images.{$i}");
                    $filename = time() . "_{$i}_" . $image->getClientOriginalName();
                    $image->move(public_path('img/pictureTarget'), $filename);
                    $additionalImagePaths[] = 'img/pictureTarget/' . $filename;
                    $additionalImageNames[] = $imageNames[$i] ?? '';
                } elseif (isset($existingImages[$i]) && !empty($existingImages[$i])) {
                    // 保留現有圖片
                    $additionalImagePaths[] = $existingImages[$i];
                    $additionalImageNames[] = $imageNames[$i] ?? '';
                }
            }
            
            // 確保圖片名稱與圖片路徑數量一致 (不過濾空值，保持索引對應)
            $product->pic_name_more = !empty($additionalImageNames) ? json_encode($additionalImageNames) : null;
            $product->pic_dir_more = !empty($additionalImagePaths) ? json_encode($additionalImagePaths) : null;
            
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
    
    /**
     * Display payment methods list
     */
    public function paymentMethods()
    {
        $paymentMethods = PaymentMethodModel::orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'total' => PaymentMethodModel::where('status', '!=', 'delete')->count(),
            'active' => PaymentMethodModel::where('status', 'active')->count(),
            'inactive' => PaymentMethodModel::where('status', 'inactive')->count(),
        ];
        
        return view('admin.payment-methods', compact('paymentMethods', 'stats'));
    }
    
    /**
     * Show create payment method form
     */
    public function createPaymentMethod()
    {
        return view('admin.payment-methods-create');
    }
    
    /**
     * Store a new payment method
     */
    public function storePaymentMethod(Request $request)
    {
        try {
            $request->validate([
                'method_name' => 'required|unique:payment_methods,method_name|max:20',
                'fee_percentage' => 'nullable|numeric|min:0|max:100',
                'fee_fixed' => 'nullable|numeric|min:0',
                'display_order' => 'nullable|integer|min:0',
            ]);
            
            $data = [
                'method_name' => $request->method_name,
                'description' => $request->description,
                'api_endpoint' => $request->api_endpoint,
                'merchant_id' => $request->merchant_id,
                'api_key' => $request->api_key,
                'api_secret' => $request->api_secret,
                'sandbox_merchant_id' => $request->sandbox_merchant_id,
                'sandbox_api_key' => $request->sandbox_api_key,
                'sandbox_api_secret' => $request->sandbox_api_secret,
                'display_order' => $request->display_order ?? 0,
                'fee_percentage' => $request->fee_percentage ?? 0,
                'fee_fixed' => $request->fee_fixed ?? 0,
                'status' => $request->status ?? 'active',
            ];
            
            // Handle icon upload
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                $filename = time() . '_' . $icon->getClientOriginalName();
                $icon->move(public_path('img/payment_icons'), $filename);
                $data['icon'] = 'img/payment_icons/' . $filename;
            }
            
            PaymentMethodModel::create($data);
            
            return redirect()->route('admin.payment-methods')->with('success', '支付方式已新增');
        } catch (\Exception $e) {
            return back()->with('error', '新增失敗：' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Show edit payment method form
     */
    public function editPaymentMethod($id)
    {
        $paymentMethod = PaymentMethodModel::findOrFail($id);
        return view('admin.payment-methods-edit', compact('paymentMethod'));
    }
    
    /**
     * Update a payment method
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        try {
            $paymentMethod = PaymentMethodModel::findOrFail($id);
            
            $request->validate([
                'method_name' => 'required|max:20|unique:payment_methods,method_name,' . $id,
                'fee_percentage' => 'nullable|numeric|min:0|max:100',
                'fee_fixed' => 'nullable|numeric|min:0',
                'display_order' => 'nullable|integer|min:0',
            ]);
            
            $paymentMethod->method_name = $request->method_name;
            $paymentMethod->description = $request->description;
            $paymentMethod->api_endpoint = $request->api_endpoint;
            $paymentMethod->merchant_id = $request->merchant_id;
            $paymentMethod->api_key = $request->api_key;
            $paymentMethod->api_secret = $request->api_secret;
            $paymentMethod->sandbox_merchant_id = $request->sandbox_merchant_id;
            $paymentMethod->sandbox_api_key = $request->sandbox_api_key;
            $paymentMethod->sandbox_api_secret = $request->sandbox_api_secret;
            $paymentMethod->display_order = $request->display_order ?? 0;
            $paymentMethod->fee_percentage = $request->fee_percentage ?? 0;
            $paymentMethod->fee_fixed = $request->fee_fixed ?? 0;
            $paymentMethod->status = $request->status ?? 'active';
            
            // Handle icon upload
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                $filename = time() . '_' . $icon->getClientOriginalName();
                $icon->move(public_path('img/payment_icons'), $filename);
                $paymentMethod->icon = 'img/payment_icons/' . $filename;
            }
            
            $paymentMethod->save();
            
            return redirect()->route('admin.payment-methods')->with('success', '支付方式已更新');
        } catch (\Exception $e) {
            return back()->with('error', '更新失敗：' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Delete a payment method
     */
    public function deletePaymentMethod($id)
        {
            try {
                $paymentMethod = PaymentMethodModel::findOrFail($id);
                $paymentMethod->status = 'delete';
                $paymentMethod->save();
                return response()->json(['success' => true, 'message' => '支付方式已刪除']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }
    
    /**
     * Update payment method display order
     */
    public function updatePaymentMethodOrder(Request $request)
    {
        try {
            $order = $request->input('order', []);
            
            foreach ($order as $index => $id) {
                PaymentMethodModel::where('id', $id)->update(['display_order' => $index + 1]);
            }
            
            return response()->json(['success' => true, 'message' => '順序已更新']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
