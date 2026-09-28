<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::post('/product/store', [AdminController::class, 'storeProduct'])->name('admin.product.store');
    Route::post('/product/update/{id}', [AdminController::class, 'updateProduct'])->name('admin.product.update');
    Route::post('/product/delete/{id}', [AdminController::class, 'deleteProduct'])->name('admin.product.delete');
    Route::post('/product/toggle-zero-waste/{id}', [AdminController::class, 'toggleProductZeroWaste'])->name('admin.product.toggle_zero_waste');
    Route::post('/product/toggle-status/{id}', [AdminController::class, 'toggleProductStatus'])->name('admin.product.toggle_status');

    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::post('/order/status/{id}', [AdminController::class, 'updateOrderStatus'])->name('admin.order.update_status');
    Route::post('/verify-qr', [AdminController::class, 'verifyQrToken'])->name('admin.verify_qr');

    Route::get('/farmers', [AdminController::class, 'farmers'])->name('admin.farmers');
    Route::post('/farmer/toggle-approval/{id}', [AdminController::class, 'toggleFarmerApproval'])->name('admin.farmer.toggle_approval');

    Route::get('/markets', [AdminController::class, 'markets'])->name('admin.markets');
    Route::post('/market/store', [AdminController::class, 'storeMarket'])->name('admin.market.store');
    Route::post('/market/update/{id}', [AdminController::class, 'updateMarket'])->name('admin.market.update');
    Route::post('/market/delete/{id}', [AdminController::class, 'deleteMarket'])->name('admin.market.delete');

    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export-csv', [AdminController::class, 'exportReportCsv'])->name('admin.reports.export_csv');
});