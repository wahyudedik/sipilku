<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [\App\Http\Controllers\ProductController::class, 'index'])->name('home');
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

// Public Service Routes
Route::get('/services', [\App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [\App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');

// Cart Routes (Public)
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::get('/cart/count', [\App\Http\Controllers\CartController::class, 'count'])->name('cart.count');
Route::post('/cart/add/{product}', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{product}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

Route::get('/dashboard', [\App\Http\Controllers\Buyer\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Checkout Routes
    Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');

    // Order Routes
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');

    // Download Routes
    Route::get('/downloads/history', [\App\Http\Controllers\DownloadController::class, 'history'])->name('downloads.history');
    Route::get('/downloads/{order}', [\App\Http\Controllers\DownloadController::class, 'download'])->name('downloads.download');
    Route::get('/downloads/token/{token}', [\App\Http\Controllers\DownloadController::class, 'downloadByToken'])->name('downloads.token');

    // Quote Request Routes
    Route::get('/services/{service}/quote', [\App\Http\Controllers\QuoteRequestController::class, 'create'])->name('quote-requests.create');
    Route::post('/services/{service}/quote', [\App\Http\Controllers\QuoteRequestController::class, 'store'])->name('quote-requests.store');

    // Buyer Quote Request Management
    Route::get('/my-quotes', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'index'])->name('buyer.quote-requests.index');
    Route::get('/my-quotes/{quoteRequest}', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'show'])->name('buyer.quote-requests.show');
    Route::post('/my-quotes/{quoteRequest}/accept', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'accept'])->name('buyer.quote-requests.accept');

    // Tools/Calculator Routes
    Route::get('/tools', [\App\Http\Controllers\ToolsController::class, 'index'])->name('tools.index');
    Route::get('/tools/rab', [\App\Http\Controllers\ToolsController::class, 'rab'])->name('tools.rab');
    Route::post('/tools/rab/calculate', [\App\Http\Controllers\ToolsController::class, 'calculateRab'])->name('tools.rab.calculate');
    Route::get('/tools/volume-material', [\App\Http\Controllers\ToolsController::class, 'volumeMaterial'])->name('tools.volume-material');
    Route::post('/tools/volume-material/calculate', [\App\Http\Controllers\ToolsController::class, 'calculateVolumeMaterial'])->name('tools.volume-material.calculate');
    Route::get('/tools/struktur', [\App\Http\Controllers\ToolsController::class, 'struktur'])->name('tools.struktur');
    Route::post('/tools/struktur/calculate', [\App\Http\Controllers\ToolsController::class, 'calculateStruktur'])->name('tools.struktur.calculate');
    Route::get('/tools/pondasi', [\App\Http\Controllers\ToolsController::class, 'pondasi'])->name('tools.pondasi');
    Route::post('/tools/pondasi/calculate', [\App\Http\Controllers\ToolsController::class, 'calculatePondasi'])->name('tools.pondasi.calculate');
    Route::get('/tools/estimasi-waktu', [\App\Http\Controllers\ToolsController::class, 'estimasiWaktu'])->name('tools.estimasi-waktu');
    Route::post('/tools/estimasi-waktu/calculate', [\App\Http\Controllers\ToolsController::class, 'calculateEstimasiWaktu'])->name('tools.estimasi-waktu.calculate');
    Route::get('/tools/overhead-profit', [\App\Http\Controllers\ToolsController::class, 'overheadProfit'])->name('tools.overhead-profit');
    Route::post('/tools/overhead-profit/calculate', [\App\Http\Controllers\ToolsController::class, 'calculateOverheadProfit'])->name('tools.overhead-profit.calculate');
    Route::post('/tools/save', [\App\Http\Controllers\ToolsController::class, 'save'])->name('tools.save');
    Route::get('/tools/history', [\App\Http\Controllers\ToolsController::class, 'history'])->name('tools.history');
    Route::get('/tools/{calculation}', [\App\Http\Controllers\ToolsController::class, 'show'])->name('tools.show');
    Route::delete('/tools/{calculation}', [\App\Http\Controllers\ToolsController::class, 'destroy'])->name('tools.destroy');
    Route::post('/my-quotes/{quoteRequest}/reject', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'reject'])->name('buyer.quote-requests.reject');
    Route::get('/my-quotes/compare', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'compare'])->name('buyer.quote-requests.compare');

    // Notification Routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/preferences', [\App\Http\Controllers\NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::post('/notifications/preferences', [\App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('notifications.update-preferences');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    // Message/Chat Routes
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [\App\Http\Controllers\MessageController::class, 'chat'])->name('messages.chat');
    Route::get('/messages/{user}/order/{order}', [\App\Http\Controllers\MessageController::class, 'chat'])->name('messages.chat.order');
    Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/mark-read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::get('/messages/{message}/attachment/{index}', [\App\Http\Controllers\MessageController::class, 'downloadAttachment'])->name('messages.download-attachment');

    // Payment Routes
    Route::get('/payments/history', [\App\Http\Controllers\PaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/process/{order}', [\App\Http\Controllers\PaymentController::class, 'process'])->name('payments.process');
    Route::get('/payments/status/{order}', [\App\Http\Controllers\PaymentController::class, 'status'])->name('payments.status');

    // Balance Routes
    Route::get('/balance', [\App\Http\Controllers\BalanceController::class, 'index'])->name('balance.index');
    Route::get('/balance/top-up', [\App\Http\Controllers\BalanceController::class, 'topUp'])->name('balance.top-up');
    Route::post('/balance/top-up', [\App\Http\Controllers\BalanceController::class, 'processTopUp'])->name('balance.process-top-up');
    Route::get('/balance/top-up/{transaction}', [\App\Http\Controllers\BalanceController::class, 'topUpStatus'])->name('balance.top-up-status');
});

// Payment Callback (no auth required)
Route::post('/payments/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payments.callback');
Route::post('/balance/callback', [\App\Http\Controllers\BalanceController::class, 'callbackTopUp'])->name('balance.callback');

    // Seller Routes
Route::middleware(['auth', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Seller\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', \App\Http\Controllers\Seller\ProductController::class);
    Route::resource('services', \App\Http\Controllers\Seller\ServiceController::class);
    
    // Seller Quote Request Management
    Route::get('quote-requests', [\App\Http\Controllers\Seller\QuoteRequestController::class, 'index'])->name('quote-requests.index');
    Route::get('quote-requests/{quoteRequest}', [\App\Http\Controllers\Seller\QuoteRequestController::class, 'show'])->name('quote-requests.show');
    Route::post('quote-requests/{quoteRequest}/respond', [\App\Http\Controllers\Seller\QuoteRequestController::class, 'respond'])->name('quote-requests.respond');

    // Commission Management
    Route::get('commissions', [\App\Http\Controllers\Seller\CommissionController::class, 'index'])->name('commissions.index');
    Route::get('commissions/report', [\App\Http\Controllers\Seller\CommissionController::class, 'report'])->name('commissions.report');
    Route::get('commissions/payout', [\App\Http\Controllers\Seller\CommissionController::class, 'requestPayout'])->name('commissions.payout');
    Route::post('commissions/payout', [\App\Http\Controllers\Seller\CommissionController::class, 'processPayout'])->name('commissions.process-payout');
    Route::get('commissions/withdrawal/{withdrawal}', [\App\Http\Controllers\Seller\CommissionController::class, 'showWithdrawal'])->name('commissions.show-withdrawal');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'store']);
    Route::post('users/{user}/approve-seller', [\App\Http\Controllers\Admin\UserController::class, 'approveSeller'])->name('users.approve-seller');
    Route::post('users/{user}/reject-seller', [\App\Http\Controllers\Admin\UserController::class, 'rejectSeller'])->name('users.reject-seller');
    Route::post('users/{user}/activate', [\App\Http\Controllers\Admin\UserController::class, 'activate'])->name('users.activate');
    Route::post('users/{user}/deactivate', [\App\Http\Controllers\Admin\UserController::class, 'deactivate'])->name('users.deactivate');
    
    Route::get('products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->name('products.show');
    Route::post('products/{product}/approve', [\App\Http\Controllers\Admin\ProductController::class, 'approve'])->name('products.approve');
    Route::post('products/{product}/reject', [\App\Http\Controllers\Admin\ProductController::class, 'reject'])->name('products.reject');
    Route::post('products/bulk-action', [\App\Http\Controllers\Admin\ProductController::class, 'bulkAction'])->name('products.bulk-action');
    Route::delete('products/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.destroy');

    // Service Management
    Route::get('services', [\App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('services.index');
    Route::get('services/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'show'])->name('services.show');
    Route::post('services/{service}/approve', [\App\Http\Controllers\Admin\ServiceController::class, 'approve'])->name('services.approve');
    Route::post('services/{service}/reject', [\App\Http\Controllers\Admin\ServiceController::class, 'reject'])->name('services.reject');
    Route::post('services/bulk-action', [\App\Http\Controllers\Admin\ServiceController::class, 'bulkAction'])->name('services.bulk-action');
    Route::delete('services/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('services.destroy');
    
    // Category Management
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    
    // Landing Page Builder
    Route::get('landing-page', [\App\Http\Controllers\Admin\LandingPageController::class, 'index'])->name('landing-page.index');
    Route::get('landing-page/edit', [\App\Http\Controllers\Admin\LandingPageController::class, 'edit'])->name('landing-page.edit');
    Route::post('landing-page', [\App\Http\Controllers\Admin\LandingPageController::class, 'update'])->name('landing-page.update');

    // Order Management
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/confirm-payment', [\App\Http\Controllers\Admin\OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::post('orders/{order}/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');
});

require __DIR__.'/auth.php';
