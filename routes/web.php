<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\homeApiCtlr;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\contactCtlr;
use App\Http\Controllers\checkController;
use App\Http\Controllers\purchasedCtlr;
use App\Http\Controllers\payController;
use App\Http\Controllers\listController;
use App\Http\Controllers\editCtlr;
use App\Http\Controllers\MailTestController;
use App\Http\Controllers\AiApiCtlr;
use App\Http\Controllers\AdminController;


// gmail SMTP test
Route::get('/send-test-mail', [MailTestController::class, 'send']);
Route::get('/test-mail', [MailTestController::class, 'test']);
Route::get('/view_mail', [purchasedCtlr::class, 'view_mail'])->name('view_mail');

// test openAI
Route::get('/testApi_show', [AiApiCtlr::class, 'testApi_show'])->name('testApi_show');
Route::post('/testApi_request', [AiApiCtlr::class, 'testApi_request'])->name('testApi_request');

Route::middleware(['guest'])->group(function() {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);

    // google login
    Route::get('/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

    Route::get('/verification', [AuthController::class, 'verification'])->name('verification');
    Route::post('/verification_check', [AuthController::class, 'verification_check'])->name('verification_check');
    Route::post('/verification_resend', [AuthController::class, 'verification_resend'])->name('verification_resend');
    Route::post('/verification_to_admin', [AuthController::class, 'verification_to_admin'])->name('verification_to_admin');
});
Route::middleware(['auth'])->group(function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/want', [checkController::class, 'want'])->name('want');

    Route::post('/purchase', [purchasedCtlr::class, 'purchase'])->name('purchase');

    Route::get('/check', [checkController::class, 'check_show'])->name('check_show');
    Route::post('/check/store', [checkController::class, 'check_store']) ->name('check_store');

    Route::get('/pay', [purchasedCtlr::class, 'pay_show'])->name('pay_show');
    Route::post('/pay/1', [purchasedCtlr::class, 'pay_to_shop'])->name('pay_to_shop');
    Route::post('/pay/2', [purchasedCtlr::class, 'pay_to_home'])->name('pay_to_home');
    Route::post('/pay/3', [purchasedCtlr::class, 'pay_name'])->name('pay_name');
    Route::post('/pay/4', [purchasedCtlr::class, 'pay_account'])->name('pay_account');
    Route::post('/pay/5', [purchasedCtlr::class, 'pay_confirm'])->name('pay_confirm');

    Route::get('/list', [listController::class, 'list_show'])->name('list_show');
    Route::post('/list_store', [listController::class, 'list_store'])->name('list_store');

    Route::get('/edit', [editCtlr::class, 'edit_show'])->name('edit_show');
    Route::post('/edit/edit_product_store', [editCtlr::class, 'edit_product_store'])->name('edit_product_store');
    Route::post('/edit/edit_product_add', [editCtlr::class, 'edit_product_add'])->name('edit_product_add');

    Route::get('/admin', [editCtlr::class, 'admin_show'])->name('admin_show');

    Route::post('/edit/edit_product_delete', [editCtlr::class, 'edit_product_delete'])->name('edit_product_delete');

    Route::get('/member_edit', [AuthController::class, 'member_edit'])->name('member_edit');
    Route::post('/member_edit_save', [AuthController::class, 'member_edit_save'])->name('member_edit_save');
    Route::get('/order_query', [AuthController::class, 'order_query'])->name('order_query');

    Route::get('/map', [payController::class, 'map'])->name('map');

});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function() {
    Route::post('/contacts/reply', [\App\Http\Controllers\contactCtlr::class, 'reply']);
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Products Management
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('products.delete');
    Route::post('/products/upload-image', [AdminController::class, 'uploadImage'])->name('products.upload-image');
    
    // Orders Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'getOrder'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::delete('/orders/{id}', [AdminController::class, 'deleteOrder'])->name('orders.delete');
    
    // Contacts Management
    Route::get('/contacts', [AdminController::class, 'contacts'])->name('contacts');
    Route::get('/contacts/{id}', [AdminController::class, 'getContact'])->name('contacts.show');
    Route::delete('/contacts/{id}', [AdminController::class, 'deleteContact'])->name('contacts.delete');
    
    // Mail List Management
    Route::get('/maillist', [AdminController::class, 'maillist'])->name('maillist');
    Route::get('/maillist/compose', function() { return view('admin.maillist-compose'); })->name('maillist.compose');
    Route::post('/maillist/{id}/toggle', [AdminController::class, 'toggleMailStatus'])->name('maillist.toggle');
    Route::post('/maillist/{id}/toggle-stock-notification', [AdminController::class, 'toggleStockNotification'])->name('maillist.toggle-stock-notification');
    Route::delete('/maillist/{id}', [AdminController::class, 'deleteMail'])->name('maillist.delete');
    
    // Marquee Management
    Route::get('/marquee', [AdminController::class, 'marquee'])->name('marquee');
    Route::post('/marquee', [AdminController::class, 'storeMarquee'])->name('marquee.store');
    Route::put('/marquee/{id}', [AdminController::class, 'updateMarquee'])->name('marquee.update');
    Route::post('/marquee/update-order', [AdminController::class, 'updateMarqueeOrder'])->name('marquee.updateOrder');
    Route::delete('/marquee/{id}', [AdminController::class, 'destroyMarquee'])->name('marquee.destroy');
    
    // Payment Methods Management
    Route::get('/payment-methods', [AdminController::class, 'paymentMethods'])->name('payment-methods');
    Route::get('/payment-methods/create', [AdminController::class, 'createPaymentMethod'])->name('payment-methods.create');
    Route::post('/payment-methods', [AdminController::class, 'storePaymentMethod'])->name('payment-methods.store');
    Route::get('/payment-methods/{id}/edit', [AdminController::class, 'editPaymentMethod'])->name('payment-methods.edit');
    Route::put('/payment-methods/{id}', [AdminController::class, 'updatePaymentMethod'])->name('payment-methods.update');
    Route::delete('/payment-methods/{id}', [AdminController::class, 'deletePaymentMethod'])->name('payment-methods.delete');
    Route::post('/payment-methods/update-order', [AdminController::class, 'updatePaymentMethodOrder'])->name('payment-methods.updateOrder');
});

Route::get('/contact', [contactCtlr::class, 'report_show'])->name('report_show');
Route::post('/contact/store', [contactCtlr::class, 'reporting']) ->name('reporting');

Route::get('/', [homeApiCtlr::class, 'tohome'] )->name('home');
Route::get('/{search}', [homeApiCtlr::class, 'toHome_with_search'] )->name('home_with_search');
Route::post('/', [homeApiCtlr::class, 'toHome_words_search'] )->name('toHome_words_search');
Route::get('/itemPage/{id}', [homeApiCtlr::class, 'toItemPage'])->name('itemPage');








