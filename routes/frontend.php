<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\Frontend\MarketplaceController;
use App\Http\Controllers\Frontend\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'index'])->name('home');
Route::get('/explorer', [MarketplaceController::class, 'marketExplorer'])->name('market.explorer');
Route::get('/farmer/{slug}', [MarketplaceController::class, 'farmerShow'])->name('farmer.show');
Route::get('/product/{slug}', [MarketplaceController::class, 'productShow'])->name('product.show');
Route::post('/api/faq-bot', [MarketplaceController::class, 'faqBot'])->name('api.faq.bot');
Route::get('/terms-of-service', [MarketplaceController::class, 'termsOfService'])->name('legal.terms');
Route::get('/privacy-policy', [MarketplaceController::class, 'privacyPolicy'])->name('legal.privacy');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/basket', [CustomerController::class, 'cart'])->name('customer.cart');
Route::post('/basket/add', [CustomerController::class, 'addToCart'])->name('customer.cart.add');
Route::post('/basket/update', [CustomerController::class, 'updateCart'])->name('customer.cart.update');
Route::post('/basket/remove/{productId}', [CustomerController::class, 'removeFromCart'])->name('customer.cart.remove');

Route::middleware(['auth', 'role:customer'])->prefix('customer')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::post('/checkout/process', [CustomerController::class, 'processOrder'])->name('customer.order.process');
    Route::get('/order/{id}', [CustomerController::class, 'orderShow'])->name('customer.order.show');
    Route::get('/pickup-route', [CustomerController::class, 'pickupRoute'])->name('customer.pickup.route');
    Route::post('/review', [CustomerController::class, 'storeReview'])->name('customer.review.store');
    Route::post('/favorite/{farmerId}', [CustomerController::class, 'toggleFavorite'])->name('customer.favorite.toggle');
});

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
    Route::get('/inventory', [VendorController::class, 'inventory'])->name('vendor.inventory');
    Route::post('/product/store', [VendorController::class, 'storeProduct'])->name('vendor.product.store');
    Route::post('/product/update/{id}', [VendorController::class, 'updateProduct'])->name('vendor.product.update');
    Route::post('/product/delete/{id}', [VendorController::class, 'deleteProduct'])->name('vendor.product.delete');
    Route::post('/product/toggle-zero-waste/{id}', [VendorController::class, 'toggleZeroWaste'])->name('vendor.product.toggle_zero_waste');
    Route::post('/product/toggle-status/{id}', [VendorController::class, 'toggleStockStatus'])->name('vendor.product.toggle_status');
    Route::post('/inventory/duplicate-weekly', [VendorController::class, 'duplicateLastWeekTemplate'])->name('vendor.inventory.duplicate_weekly');
    Route::get('/orders', [VendorController::class, 'orders'])->name('vendor.orders');
    Route::post('/order/status/{id}', [VendorController::class, 'updateOrderStatus'])->name('vendor.order.update_status');
    Route::post('/verify-qr', [VendorController::class, 'verifyQrToken'])->name('vendor.verify_qr');
    Route::post('/review/reply/{id}', [VendorController::class, 'replyReview'])->name('vendor.review.reply');
    Route::get('/profile', [VendorController::class, 'profile'])->name('vendor.profile');
    Route::post('/profile/update', [VendorController::class, 'updateProfile'])->name('vendor.profile.update');
});