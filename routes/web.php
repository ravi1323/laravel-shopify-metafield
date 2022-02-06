<?php

use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ShopifyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['verify.shopify'])->group(function () {
    Route::get('/', [ShopifyController::class, 'index'])->name('home');
    Route::get('/shop-create', [ShopifyController::class, 'create'])->name('shop_metafield_create');
    Route::post('/store-shop-metafield', [ShopifyController::class, 'store'])->name('store_shop_metafield');
    Route::get('/edit_shop_metafield', [ShopifyController::class, 'edit'])->name('edit_shop_metafield');
    Route::put('/update_shop_metafield', [ShopifyController::class, 'update'])->name('update_shop_metafield');
    Route::delete('/delete-shop-metafield', [ShopifyController::class, 'destroy'])->name('delete-shop-metafield');


    Route::get('/product', [ProductsController::class, 'index'])->name('product');
    Route::get('/product_metafield_create', [ProductsController::class, 'create'])->name('product_metafield_create');
    Route::post('/store-product-metafield', [ProductsController::class, 'store'])->name('store_product_metafield');
    Route::get('/edit_product_metafield', [ProductsController::class, 'edit'])->name('edit_product_metafield');
    Route::put('/update_product_metafield', [ProductsController::class, 'update'])->name('update_product_metafield');
    Route::view('/customer', 'dashboard.customer_metafield')->name('customer');
    Route::view('/collection', 'dashboard.collection_metafield')->name('collection');
    Route::view('/variant', 'dashboard.variant_metafield')->name('variant');
});
