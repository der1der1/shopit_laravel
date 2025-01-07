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





Route::middleware(['guest'])->group(function() {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});
Route::middleware(['auth'])->group(function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/want', [purchasedCtlr::class, 'want'])->name('want');

    Route::post('/purchase', [purchasedCtlr::class, 'purchase'])->name('purchase');

    Route::get('/check', [checkController::class, 'check_show'])->name('check_show');
    Route::post('/check/store', [checkController::class, 'check_store']) ->name('check_store');

    Route::get('/pay', [payController::class, 'pay_show'])->name('pay_show');
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
    

    Route::post('/edit/edit_product_delete', [editCtlr::class, 'edit_product_delete'])->name('edit_product_delete');

});

Route::get('/contact', [contactCtlr::class, 'report_show'])->name('report_show');
Route::post('/contact/store', [contactCtlr::class, 'reporting']) ->name('reporting');

Route::get('/', [homeApiCtlr::class, 'tohome'] )->name('home');
Route::get('/{search}', [homeApiCtlr::class, 'toHome_with_search'] )->name('home_with_search');
Route::post('/', [homeApiCtlr::class, 'toHome_words_search'] )->name('toHome_words_search');
Route::get('/itemPage/{id}', [homeApiCtlr::class, 'toItemPage'])->name('itemPage');




