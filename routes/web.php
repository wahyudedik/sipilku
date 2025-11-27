<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [\App\Http\Controllers\ProductController::class, 'index'])->name('home');
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

// Public Store Routes
Route::get('/stores', [\App\Http\Controllers\Store\StoreController::class, 'index'])->name('stores.index');
Route::get('/stores/find-nearest', [\App\Http\Controllers\Store\GeolocationController::class, 'findNearest'])->name('stores.find-nearest');
Route::get('/stores/search', [\App\Http\Controllers\Store\GeolocationController::class, 'search'])->name('stores.search');
Route::get('/stores/recommendations', [\App\Http\Controllers\Store\GeolocationController::class, 'recommendations'])->name('stores.recommendations');
Route::get('/stores/{store}', [\App\Http\Controllers\Store\StoreController::class, 'show'])->name('stores.show');

// Public Factory Routes
Route::get('/factories', [\App\Http\Controllers\Factory\FactoryController::class, 'index'])->name('factories.index');
Route::get('/factories/find-nearest', [\App\Http\Controllers\Factory\FactoryGeolocationController::class, 'findNearest'])->name('factories.find-nearest');
Route::get('/factories/search', [\App\Http\Controllers\Factory\FactoryGeolocationController::class, 'search'])->name('factories.search');
Route::get('/factories/map', [\App\Http\Controllers\Factory\FactoryGeolocationController::class, 'map'])->name('factories.map');
Route::get('/factories/recommendations', [\App\Http\Controllers\RecommendationController::class, 'nearestFactories'])->name('factories.recommendations');
Route::get('/factories/comparison', [\App\Http\Controllers\Factory\FactoryComparisonController::class, 'index'])->name('factories.comparison.index');
Route::get('/factories/comparison/price', [\App\Http\Controllers\Factory\FactoryComparisonController::class, 'comparePrice'])->name('factories.comparison.price');
Route::get('/factories/comparison/quality', [\App\Http\Controllers\Factory\FactoryComparisonController::class, 'compareQuality'])->name('factories.comparison.quality');
Route::get('/factories/comparison/multiple', [\App\Http\Controllers\Factory\FactoryComparisonController::class, 'compareMultiple'])->name('factories.comparison.multiple');
Route::get('/factories/{factory}', [\App\Http\Controllers\Factory\FactoryController::class, 'show'])->name('factories.show');

// Public Service Routes
Route::get('/services', [\App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [\App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');

// Recommendation Routes
Route::get('/recommendations/stores/nearest', [\App\Http\Controllers\RecommendationController::class, 'nearestStores'])->name('recommendations.stores.nearest');
Route::get('/recommendations/factories/nearest', [\App\Http\Controllers\RecommendationController::class, 'nearestFactories'])->name('recommendations.factories.nearest');
Route::get('/recommendations/factories/type/{factoryTypeSlug}', [\App\Http\Controllers\RecommendationController::class, 'factoryTypeRecommendations'])->name('recommendations.factories.type');
Route::get('/recommendations/smart', [\App\Http\Controllers\RecommendationController::class, 'smartRecommendations'])->name('recommendations.smart');

// Comparison Routes
Route::get('/comparisons/stores/prices', [\App\Http\Controllers\ComparisonController::class, 'compareStorePrices'])->name('comparisons.stores.prices');
Route::get('/comparisons/stores', [\App\Http\Controllers\ComparisonController::class, 'compareStores'])->name('comparisons.stores');
Route::get('/comparisons/factories/total-cost', [\App\Http\Controllers\ComparisonController::class, 'compareFactoryTotalCost'])->name('comparisons.factories.total-cost');
Route::get('/comparisons/factories/quality', [\App\Http\Controllers\ComparisonController::class, 'compareFactoryQuality'])->name('comparisons.factories.quality');
Route::get('/comparisons/factories', [\App\Http\Controllers\ComparisonController::class, 'compareFactories'])->name('comparisons.factories');

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

    // Store Management
    Route::get('/stores/my-store', [\App\Http\Controllers\Store\StoreController::class, 'myStore'])->name('stores.my-store');
    Route::get('/store/dashboard', [\App\Http\Controllers\Store\DashboardController::class, 'index'])->name('store.dashboard');
    Route::get('/stores/create', [\App\Http\Controllers\Store\StoreController::class, 'create'])->name('stores.create');
    Route::post('/stores', [\App\Http\Controllers\Store\StoreController::class, 'store'])->name('stores.store');
    Route::get('/stores/{store}/edit', [\App\Http\Controllers\Store\StoreController::class, 'edit'])->name('stores.edit');
    Route::put('/stores/{store}', [\App\Http\Controllers\Store\StoreController::class, 'update'])->name('stores.update');
    Route::delete('/stores/{store}', [\App\Http\Controllers\Store\StoreController::class, 'destroy'])->name('stores.destroy');

    // Store Location Management
    Route::get('/stores/{store}/locations', [\App\Http\Controllers\Store\StoreLocationController::class, 'index'])->name('stores.locations.index');
    Route::get('/stores/{store}/locations/create', [\App\Http\Controllers\Store\StoreLocationController::class, 'create'])->name('stores.locations.create');
    Route::post('/stores/{store}/locations', [\App\Http\Controllers\Store\StoreLocationController::class, 'store'])->name('stores.locations.store');
    Route::get('/stores/{store}/locations/{location}/edit', [\App\Http\Controllers\Store\StoreLocationController::class, 'edit'])->name('stores.locations.edit');
    Route::put('/stores/{store}/locations/{location}', [\App\Http\Controllers\Store\StoreLocationController::class, 'update'])->name('stores.locations.update');
    Route::delete('/stores/{store}/locations/{location}', [\App\Http\Controllers\Store\StoreLocationController::class, 'destroy'])->name('stores.locations.destroy');

    // Store Product Management
    Route::get('/stores/{store}/products', [\App\Http\Controllers\Store\StoreProductController::class, 'index'])->name('stores.products.index');
    Route::get('/stores/{store}/products/create', [\App\Http\Controllers\Store\StoreProductController::class, 'create'])->name('stores.products.create');
    Route::post('/stores/{store}/products', [\App\Http\Controllers\Store\StoreProductController::class, 'store'])->name('stores.products.store');
    Route::get('/stores/{store}/products/{product}', [\App\Http\Controllers\Store\StoreProductController::class, 'show'])->name('stores.products.show');
    Route::get('/stores/{store}/products/{product}/edit', [\App\Http\Controllers\Store\StoreProductController::class, 'edit'])->name('stores.products.edit');
    Route::put('/stores/{store}/products/{product}', [\App\Http\Controllers\Store\StoreProductController::class, 'update'])->name('stores.products.update');
    Route::delete('/stores/{store}/products/{product}', [\App\Http\Controllers\Store\StoreProductController::class, 'destroy'])->name('stores.products.destroy');
    Route::post('/stores/{store}/products/bulk-import', [\App\Http\Controllers\Store\StoreProductController::class, 'bulkImport'])->name('stores.products.bulk-import');
    Route::post('/stores/{store}/products/bulk-update-stock', [\App\Http\Controllers\Store\StoreProductController::class, 'bulkUpdateStock'])->name('stores.products.bulk-update-stock');
    Route::post('/stores/{store}/products/{product}/availability', [\App\Http\Controllers\Store\StoreProductController::class, 'updateAvailability'])->name('stores.products.update-availability');

    // Factory Management
    Route::get('/factories/my-factory', [\App\Http\Controllers\Factory\FactoryController::class, 'myFactory'])->name('factories.my-factory');
    Route::get('/factories/create', [\App\Http\Controllers\Factory\FactoryController::class, 'create'])->name('factories.create');
    Route::post('/factories', [\App\Http\Controllers\Factory\FactoryController::class, 'store'])->name('factories.store');
    Route::get('/factories/{factory}/edit', [\App\Http\Controllers\Factory\FactoryController::class, 'edit'])->name('factories.edit');
    Route::put('/factories/{factory}', [\App\Http\Controllers\Factory\FactoryController::class, 'update'])->name('factories.update');
    Route::delete('/factories/{factory}', [\App\Http\Controllers\Factory\FactoryController::class, 'destroy'])->name('factories.destroy');

    // Factory Location Management
    Route::get('/factories/{factory}/locations', [\App\Http\Controllers\Factory\FactoryLocationController::class, 'index'])->name('factories.locations.index');
    Route::get('/factories/{factory}/locations/create', [\App\Http\Controllers\Factory\FactoryLocationController::class, 'create'])->name('factories.locations.create');
    Route::post('/factories/{factory}/locations', [\App\Http\Controllers\Factory\FactoryLocationController::class, 'store'])->name('factories.locations.store');
    Route::get('/factories/{factory}/locations/{location}/edit', [\App\Http\Controllers\Factory\FactoryLocationController::class, 'edit'])->name('factories.locations.edit');
    Route::put('/factories/{factory}/locations/{location}', [\App\Http\Controllers\Factory\FactoryLocationController::class, 'update'])->name('factories.locations.update');
    Route::delete('/factories/{factory}/locations/{location}', [\App\Http\Controllers\Factory\FactoryLocationController::class, 'destroy'])->name('factories.locations.destroy');

    // Factory Delivery Cost Calculator
    Route::post('/factories/calculate-delivery-cost', [\App\Http\Controllers\Factory\FactoryGeolocationController::class, 'calculateDeliveryCost'])->name('factories.calculate-delivery-cost');

    // Factory Quote Management
    Route::get('/factories/{factory}/quotes', [\App\Http\Controllers\Factory\FactoryQuoteController::class, 'index'])->name('factories.quotes.index');
    Route::get('/factories/{factory}/quotes/{factoryRequest}', [\App\Http\Controllers\Factory\FactoryQuoteController::class, 'show'])->name('factories.quotes.show');
    Route::post('/factories/{factory}/quotes/{factoryRequest}/quote', [\App\Http\Controllers\Factory\FactoryQuoteController::class, 'quote'])->name('factories.quotes.quote');
    Route::post('/factories/{factory}/quotes/{factoryRequest}/update-delivery-status', [\App\Http\Controllers\Factory\FactoryQuoteController::class, 'updateDeliveryStatus'])->name('factories.quotes.update-delivery-status');

    // Factory Dashboard
    Route::get('/factories/{factory}/dashboard', [\App\Http\Controllers\Factory\DashboardController::class, 'index'])->name('factories.dashboard');

    // Factory Analytics (owner/admin)
    Route::get('/factories/{factory}/analytics', [\App\Http\Controllers\Factory\FactoryAnalyticsController::class, 'index'])->name('factories.analytics.index');
    Route::get('/factories/{factory}/analytics/review-trends', [\App\Http\Controllers\Factory\FactoryAnalyticsController::class, 'reviewTrends'])->name('factories.analytics.review-trends');
    Route::get('/factories/type/{type}/analytics/product-popularity', [\App\Http\Controllers\Factory\FactoryAnalyticsController::class, 'productPopularityByType'])->name('factories.analytics.product-popularity');
    Route::get('/factories/type/{type}/analytics/compare', [\App\Http\Controllers\Factory\FactoryAnalyticsController::class, 'compareByType'])->name('factories.analytics.compare');
    Route::get('/factories/{factory}/analytics/sales', [\App\Http\Controllers\Factory\FactoryAnalyticsController::class, 'salesReport'])->name('factories.analytics.sales');
    Route::get('/factories/{factory}/analytics/dashboard', [\App\Http\Controllers\Factory\FactoryAnalyticsController::class, 'dashboard'])->name('factories.analytics.dashboard');

    // Factory Pricing Management
    Route::get('/factories/{factory}/pricing', [\App\Http\Controllers\Factory\FactoryPricingController::class, 'index'])->name('factories.pricing.index');
    Route::put('/factories/{factory}/pricing', [\App\Http\Controllers\Factory\FactoryPricingController::class, 'update'])->name('factories.pricing.update');

    // Factory Reviews Management (Factory Owner)
    Route::get('/factories/{factory}/reviews', [\App\Http\Controllers\Factory\FactoryReviewController::class, 'index'])->name('factories.reviews.index');

    // Factory Reviews (Public - Users can create reviews)
    Route::get('/factories/{factory}/reviews/list', [\App\Http\Controllers\FactoryReviewController::class, 'index'])->name('factory-reviews.index');
    Route::get('/factories/{factory}/reviews/create', [\App\Http\Controllers\FactoryReviewController::class, 'create'])->name('factory-reviews.create');
    Route::post('/factories/{factory}/reviews', [\App\Http\Controllers\FactoryReviewController::class, 'store'])->name('factory-reviews.store');
    Route::get('/factories/{factory}/reviews/{review}/edit', [\App\Http\Controllers\FactoryReviewController::class, 'edit'])->name('factory-reviews.edit');
    Route::put('/factories/{factory}/reviews/{review}', [\App\Http\Controllers\FactoryReviewController::class, 'update'])->name('factory-reviews.update');
    Route::post('/factories/{factory}/reviews/{review}/helpful', [\App\Http\Controllers\FactoryReviewController::class, 'markHelpful'])->name('factory-reviews.mark-helpful');

    // Factory Withdrawals
    Route::get('/factories/{factory}/withdrawals', [\App\Http\Controllers\Factory\FactoryWithdrawalController::class, 'index'])->name('factories.withdrawals.index');
    Route::get('/factories/{factory}/withdrawals/create', [\App\Http\Controllers\Factory\FactoryWithdrawalController::class, 'create'])->name('factories.withdrawals.create');
    Route::post('/factories/{factory}/withdrawals', [\App\Http\Controllers\Factory\FactoryWithdrawalController::class, 'store'])->name('factories.withdrawals.store');

    // Factory Product Management
    Route::get('/factories/{factory}/products', [\App\Http\Controllers\Factory\FactoryProductController::class, 'index'])->name('factories.products.index');
    Route::get('/factories/{factory}/products/create', [\App\Http\Controllers\Factory\FactoryProductController::class, 'create'])->name('factories.products.create');
    Route::post('/factories/{factory}/products', [\App\Http\Controllers\Factory\FactoryProductController::class, 'store'])->name('factories.products.store');
    Route::get('/factories/{factory}/products/{product}', [\App\Http\Controllers\Factory\FactoryProductController::class, 'show'])->name('factories.products.show');
    Route::get('/factories/{factory}/products/{product}/edit', [\App\Http\Controllers\Factory\FactoryProductController::class, 'edit'])->name('factories.products.edit');
    Route::put('/factories/{factory}/products/{product}', [\App\Http\Controllers\Factory\FactoryProductController::class, 'update'])->name('factories.products.update');
    Route::delete('/factories/{factory}/products/{product}', [\App\Http\Controllers\Factory\FactoryProductController::class, 'destroy'])->name('factories.products.destroy');
    Route::post('/factories/{factory}/products/bulk-import', [\App\Http\Controllers\Factory\FactoryProductController::class, 'bulkImport'])->name('factories.products.bulk-import');
    Route::post('/factories/{factory}/products/{product}/availability', [\App\Http\Controllers\Factory\FactoryProductController::class, 'updateAvailability'])->name('factories.products.update-availability');
    Route::post('/factories/{factory}/products/bulk-update-stock', [\App\Http\Controllers\Factory\FactoryProductController::class, 'bulkUpdateStock'])->name('factories.products.bulk-update-stock');

    // Store Category Management
    Route::get('/stores/categories', [\App\Http\Controllers\Store\StoreCategoryController::class, 'index'])->name('stores.categories.index');
    Route::get('/stores/categories/create', [\App\Http\Controllers\Store\StoreCategoryController::class, 'create'])->name('stores.categories.create');
    Route::post('/stores/categories', [\App\Http\Controllers\Store\StoreCategoryController::class, 'store'])->name('stores.categories.store');
    Route::get('/stores/categories/{category}/edit', [\App\Http\Controllers\Store\StoreCategoryController::class, 'edit'])->name('stores.categories.edit');
    Route::put('/stores/categories/{category}', [\App\Http\Controllers\Store\StoreCategoryController::class, 'update'])->name('stores.categories.update');
    Route::delete('/stores/categories/{category}', [\App\Http\Controllers\Store\StoreCategoryController::class, 'destroy'])->name('stores.categories.destroy');

    // Store Material Request Management
    Route::get('/stores/material-requests', [\App\Http\Controllers\Store\MaterialRequestController::class, 'index'])->name('stores.material-requests.index');
    Route::get('/stores/material-requests/{materialRequest}', [\App\Http\Controllers\Store\MaterialRequestController::class, 'show'])->name('stores.material-requests.show');
    Route::get('/stores/material-requests/{materialRequest}/quote', [\App\Http\Controllers\Store\MaterialRequestController::class, 'quote'])->name('stores.material-requests.quote');
    Route::post('/stores/material-requests/{materialRequest}/quote', [\App\Http\Controllers\Store\MaterialRequestController::class, 'storeQuote'])->name('stores.material-requests.store-quote');
    Route::post('/stores/material-requests/{materialRequest}/delivery-status', [\App\Http\Controllers\Store\MaterialRequestController::class, 'updateDeliveryStatus'])->name('stores.material-requests.update-delivery-status');
    Route::post('/stores/material-requests/{materialRequest}/cancel', [\App\Http\Controllers\Store\MaterialRequestController::class, 'cancel'])->name('stores.material-requests.cancel');

    // Store Order Management
    Route::get('/store/orders', [\App\Http\Controllers\Store\OrderController::class, 'index'])->name('store.orders.index');
    Route::get('/store/orders/{materialRequest}', [\App\Http\Controllers\Store\OrderController::class, 'show'])->name('store.orders.show');

    // Store Reviews Management (Store Owner)
    Route::get('/store/reviews', [\App\Http\Controllers\Store\ReviewController::class, 'index'])->name('store.reviews.index');
    Route::get('/store/reviews/{review}', [\App\Http\Controllers\Store\ReviewController::class, 'show'])->name('store.reviews.show');

    // Store Analytics & Reporting
    Route::get('/store/analytics', [\App\Http\Controllers\Store\AnalyticsController::class, 'index'])->name('store.analytics.index');

    // Store Reviews (Public - Users can create reviews)
    Route::get('/stores/{store}/reviews', [\App\Http\Controllers\StoreReviewController::class, 'index'])->name('store-reviews.index');
    Route::get('/stores/{store}/reviews/create', [\App\Http\Controllers\StoreReviewController::class, 'create'])->name('store-reviews.create');
    Route::post('/stores/{store}/reviews', [\App\Http\Controllers\StoreReviewController::class, 'store'])->name('store-reviews.store');
    Route::get('/stores/{store}/reviews/{review}/edit', [\App\Http\Controllers\StoreReviewController::class, 'edit'])->name('store-reviews.edit');
    Route::put('/stores/{store}/reviews/{review}', [\App\Http\Controllers\StoreReviewController::class, 'update'])->name('store-reviews.update');
    Route::post('/stores/{store}/reviews/{review}/helpful', [\App\Http\Controllers\StoreReviewController::class, 'markHelpful'])->name('store-reviews.mark-helpful');

    // Store Withdrawals
    Route::get('/store/withdrawals', [\App\Http\Controllers\Store\WithdrawalController::class, 'index'])->name('store.withdrawals.index');
    Route::get('/store/withdrawals/create', [\App\Http\Controllers\Store\WithdrawalController::class, 'create'])->name('store.withdrawals.create');
    Route::post('/store/withdrawals', [\App\Http\Controllers\Store\WithdrawalController::class, 'store'])->name('store.withdrawals.store');

    // Tools/Calculator Routes
    Route::get('/tools', [\App\Http\Controllers\ToolsController::class, 'index'])->name('tools.index');
    Route::get('/tools/rab', [\App\Http\Controllers\ToolsController::class, 'rab'])->name('tools.rab');
    Route::post('/tools/rab/calculate', [\App\Http\Controllers\ToolsController::class, 'calculateRab'])->name('tools.rab.calculate');
    Route::post('/tools/rab/optimized-sourcing', [\App\Http\Controllers\ToolsController::class, 'getOptimizedSourcing'])->name('tools.rab.optimized-sourcing');
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
    Route::get('/messages/{user}/material-request/{materialRequest}', [\App\Http\Controllers\MessageController::class, 'chat'])->name('messages.chat.material-request');
    Route::get('/messages/{user}/factory-request/{factoryRequest}', [\App\Http\Controllers\MessageController::class, 'chat'])->name('messages.chat.factory-request');
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

    // Store Management
    Route::get('stores', [\App\Http\Controllers\Admin\StoreController::class, 'index'])->name('stores.index');
    Route::get('stores/{store}', [\App\Http\Controllers\Admin\StoreController::class, 'show'])->name('stores.show');
    Route::post('stores/{store}/approve', [\App\Http\Controllers\Admin\StoreController::class, 'approve'])->name('stores.approve');
    Route::post('stores/{store}/reject', [\App\Http\Controllers\Admin\StoreController::class, 'reject'])->name('stores.reject');
    Route::post('stores/{store}/suspend', [\App\Http\Controllers\Admin\StoreController::class, 'suspend'])->name('stores.suspend');
    Route::post('stores/{store}/activate', [\App\Http\Controllers\Admin\StoreController::class, 'activate'])->name('stores.activate');
    Route::post('stores/bulk-action', [\App\Http\Controllers\Admin\StoreController::class, 'bulkAction'])->name('stores.bulk-action');
    Route::delete('stores/{store}', [\App\Http\Controllers\Admin\StoreController::class, 'destroy'])->name('stores.destroy');

    // Factory Management
    Route::get('factories', [\App\Http\Controllers\Admin\FactoryController::class, 'index'])->name('factories.index');
    Route::get('factories/{factory}', [\App\Http\Controllers\Admin\FactoryController::class, 'show'])->name('factories.show');
    Route::post('factories/{factory}/approve', [\App\Http\Controllers\Admin\FactoryController::class, 'approve'])->name('factories.approve');
    Route::post('factories/{factory}/reject', [\App\Http\Controllers\Admin\FactoryController::class, 'reject'])->name('factories.reject');
    Route::post('factories/{factory}/suspend', [\App\Http\Controllers\Admin\FactoryController::class, 'suspend'])->name('factories.suspend');
    Route::post('factories/{factory}/activate', [\App\Http\Controllers\Admin\FactoryController::class, 'activate'])->name('factories.activate');
    Route::post('factories/bulk-action', [\App\Http\Controllers\Admin\FactoryController::class, 'bulkAction'])->name('factories.bulk-action');
    Route::delete('factories/{factory}', [\App\Http\Controllers\Admin\FactoryController::class, 'destroy'])->name('factories.destroy');

    // Factory Type Management
    Route::resource('factory-types', \App\Http\Controllers\Admin\FactoryTypeController::class);

    // Landing Page Builder
    Route::get('landing-page', [\App\Http\Controllers\Admin\LandingPageController::class, 'index'])->name('landing-page.index');
    Route::get('landing-page/edit', [\App\Http\Controllers\Admin\LandingPageController::class, 'edit'])->name('landing-page.edit');
    Route::post('landing-page', [\App\Http\Controllers\Admin\LandingPageController::class, 'update'])->name('landing-page.update');

    // Order Management
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/confirm-payment', [\App\Http\Controllers\Admin\OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::post('orders/{order}/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');

    // Financial Management
    Route::get('financial/transactions', [\App\Http\Controllers\Admin\FinancialController::class, 'transactions'])->name('financial.transactions');
    Route::get('financial/reports', [\App\Http\Controllers\Admin\FinancialController::class, 'reports'])->name('financial.reports');
    Route::get('financial/commissions', [\App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('financial.commissions');

    // Withdrawal Management
    Route::get('withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::post('withdrawals/{withdrawal}/approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('withdrawals/{withdrawal}/reject', [\App\Http\Controllers\Admin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');
    Route::post('withdrawals/bulk-approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'bulkApprove'])->name('withdrawals.bulk-approve');

    // System Settings
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
    Route::get('settings', [\App\Http\Controllers\Admin\SystemConfigController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SystemConfigController::class, 'store'])->name('settings.store');
    Route::put('settings', [\App\Http\Controllers\Admin\SystemConfigController::class, 'update'])->name('settings.update');
    Route::delete('settings/{setting}', [\App\Http\Controllers\Admin\SystemConfigController::class, 'destroy'])->name('settings.destroy');
    Route::resource('email-templates', \App\Http\Controllers\Admin\EmailTemplateController::class);
    Route::get('email-templates/{email_template}/preview', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])->name('email-templates.preview');
    Route::get('backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
    Route::post('backups', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('backups.create');
    Route::get('backups/{filename}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
    Route::delete('backups/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backups.destroy');
    Route::post('backups/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('backups.restore');
});

// Contractor Routes
Route::middleware(['auth'])->prefix('contractor')->name('contractor.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Contractor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('material-procurement', [\App\Http\Controllers\Contractor\DashboardController::class, 'materialProcurement'])->name('material-procurement');
    Route::get('factory-procurement', [\App\Http\Controllers\Contractor\DashboardController::class, 'factoryProcurement'])->name('factory-procurement');
    Route::get('factory-cost-calculator', [\App\Http\Controllers\Contractor\DashboardController::class, 'factoryCostCalculator'])->name('factory-cost-calculator');

    // Project Location Management
    Route::resource('project-locations', \App\Http\Controllers\Contractor\ProjectLocationController::class);

    // Material Requests
    Route::resource('material-requests', \App\Http\Controllers\Contractor\MaterialRequestController::class);
    Route::post('material-requests/{materialRequest}/accept', [\App\Http\Controllers\Contractor\MaterialRequestController::class, 'accept'])->name('material-requests.accept');
    Route::post('material-requests/{materialRequest}/reject', [\App\Http\Controllers\Contractor\MaterialRequestController::class, 'reject'])->name('material-requests.reject');
    Route::get('material-requests/compare', [\App\Http\Controllers\Contractor\MaterialRequestComparisonController::class, 'compare'])->name('material-requests.compare');

    // Factory Requests
    Route::resource('factory-requests', \App\Http\Controllers\Contractor\FactoryRequestController::class);
    Route::get('factory-requests/compare/{request_group_id}', [\App\Http\Controllers\Contractor\FactoryRequestComparisonController::class, 'compare'])->name('factory-requests.compare');
    Route::post('factory-requests/{factoryRequest}/accept', [\App\Http\Controllers\Contractor\FactoryRequestController::class, 'accept'])->name('factory-requests.accept');
    Route::post('factory-requests/{factoryRequest}/reject', [\App\Http\Controllers\Contractor\FactoryRequestController::class, 'reject'])->name('factory-requests.reject');

    // Contractor Recommendations
    Route::get('recommendations', [\App\Http\Controllers\RecommendationController::class, 'contractorRecommendations'])->name('recommendations');
    Route::get('recommendations/project/{projectLocation}', [\App\Http\Controllers\RecommendationController::class, 'contractorRecommendations'])->name('recommendations.project');
});

require __DIR__ . '/auth.php';
