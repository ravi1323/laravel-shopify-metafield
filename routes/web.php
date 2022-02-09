<?php

use App\Http\Controllers\collectionsController;
use App\Http\Controllers\CustomerController;
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
    // shops metafield routes
    Route::get('/', [ShopifyController::class, 'index'])->name('home');
    Route::get('/shop-create', [ShopifyController::class, 'create'])->name('shop_metafield_create');
    Route::post('/store-shop-metafield', [ShopifyController::class, 'store'])->name('store_shop_metafield');
    Route::get('/edit_shop_metafield', [ShopifyController::class, 'edit'])->name('edit_shop_metafield');
    Route::put('/update_shop_metafield', [ShopifyController::class, 'update'])->name('update_shop_metafield');
    Route::delete('/delete-shop-metafield', [ShopifyController::class, 'destroy'])->name('delete-shop-metafield');

    // products metafield routes
    Route::get('/product', [ProductsController::class, 'index'])->name('product');
    Route::get('/product_metafield_create', [ProductsController::class, 'create'])->name('product_metafield_create');
    Route::post('/store-product-metafield', [ProductsController::class, 'store'])->name('store_product_metafield');
    Route::get('/edit_product_metafield', [ProductsController::class, 'edit'])->name('edit_product_metafield');
    Route::put('/update_product_metafield', [ProductsController::class, 'update'])->name('update_product_metafield');
    Route::delete('/delete-product-metafield', [ProductsController::class, 'destroy'])->name('delete-product-metafield');


    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
    Route::get('/create_customer_metafield', [CustomerController::class, 'create'])->name('create_customer_metafield');
    Route::post('/store-customer-metafield', [CustomerController::class, 'store'])->name('store-customer-metafield');
    Route::get('/edit_customer_metafield', [CustomerController::class, 'edit'])->name('edit_customer_metafield');
    Route::put('/update_customer_metafield', [CustomerController::class, 'update'])->name('update_customer_metafield');
    Route::delete('/delete-customer-metafield', [CustomerController::class, 'destroy'])->name('delete-customer-metafield');


    Route::get('/collection', [collectionsController::class, 'index'])->name('collection');
    Route::get('/create_collection_metafield', [collectionsController::class, 'create'])->name('create_collection_metafield');
    Route::post('/store-collection-metafield', [collectionsController::class, 'store'])->name('store-collection-metafield');
    Route::get('/edit_collection_metafield', [collectionsController::class, 'edit'])->name('edit_collection_metafield');
    Route::put('/update_collection_metafield', [collectionsController::class, 'update'])->name('update_collection_metafield');
    Route::delete('/delete-collection-metafield', [collectionsController::class, 'destroy'])->name('delete-collection-metafield');

    Route::view('/variant', 'dashboard.variant_metafield')->name('variant');
});
