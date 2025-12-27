<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ProductBundleController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\FaqController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search/suggestions', [ProductController::class, 'suggestions'])->name('products.suggestions');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Sellers
Route::get('/sellers', [SellerController::class, 'index'])->name('sellers.index');
Route::get('/sellers/{seller}', [SellerController::class, 'show'])->name('sellers.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');

// Coupons
Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');
Route::post('/coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');

// Static Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/help', [PageController::class, 'help'])->name('help');

// FAQ
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// Legal Pages
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');
Route::get('/license', [PageController::class, 'license'])->name('license');
Route::get('/refund-policy', [PageController::class, 'refund'])->name('refund');

// Become a Seller (public landing page)
Route::get('/become-a-seller', [SellerApplicationController::class, 'index'])->name('become-seller');

// Product Bundles
Route::get('/bundles', [ProductBundleController::class, 'index'])->name('bundles.index');
Route::get('/bundles/{bundle:slug}', [ProductBundleController::class, 'show'])->name('bundles.show');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');

// Stripe Webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])->name('stripe.webhook');

// Social Login
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Purchases
    Route::get('/purchases', [DashboardController::class, 'purchases'])->name('purchases');

    // Downloads
    Route::get('/downloads/{orderItem}', [DownloadController::class, 'download'])->name('download');

    // Wishlist
    Route::get('/wishlist', [DashboardController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Reviews
    Route::post('/reviews/{product}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/vote', [ReviewController::class, 'vote'])->name('reviews.vote');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // Seller Application
    Route::get('/seller/apply', [SellerApplicationController::class, 'create'])->name('seller.apply');
    Route::post('/seller/apply', [SellerApplicationController::class, 'store'])->name('seller.apply.store');
    Route::get('/seller/pending', [SellerApplicationController::class, 'pending'])->name('seller.pending');

    // Seller Follow
    Route::post('/sellers/{seller}/follow', [SellerController::class, 'follow'])->name('sellers.follow');

    // Conversations
    Route::get('/messages', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/messages/new', [ConversationController::class, 'create'])->name('conversations.create');
    Route::post('/messages', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/messages/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/messages/{conversation}/reply', [ConversationController::class, 'reply'])->name('conversations.reply');

    // Support Tickets
    Route::get('/support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::get('/support/new', [SupportTicketController::class, 'create'])->name('support.create');
    Route::post('/support', [SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');
    Route::post('/support/{ticket}/close', [SupportTicketController::class, 'close'])->name('support.close');

    // Affiliate Program
    Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate.dashboard');
    Route::get('/affiliate/apply', [AffiliateController::class, 'apply'])->name('affiliate.apply');
    Route::post('/affiliate/apply', [AffiliateController::class, 'store'])->name('affiliate.apply.store');
    Route::get('/affiliate/settings', [AffiliateController::class, 'settings'])->name('affiliate.settings');
    Route::put('/affiliate/settings', [AffiliateController::class, 'updateSettings'])->name('affiliate.settings.update');
});

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Seller\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', App\Http\Controllers\Seller\ProductController::class);
    Route::get('/orders', [App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
    Route::get('/licenses', [App\Http\Controllers\Seller\LicenseController::class, 'index'])->name('licenses.index');
    Route::get('/reviews', [App\Http\Controllers\Seller\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/respond', [App\Http\Controllers\Seller\ReviewController::class, 'respond'])->name('reviews.respond');
    Route::delete('/reviews/{review}/response', [App\Http\Controllers\Seller\ReviewController::class, 'deleteResponse'])->name('reviews.response.delete');
    Route::get('/payouts', [App\Http\Controllers\Seller\PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/request', [App\Http\Controllers\Seller\PayoutController::class, 'request'])->name('payouts.request');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
