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
use App\Http\Controllers\ProductRequestController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\CustomQuoteController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\JobContractController;
use App\Http\Controllers\EscrowController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\Admin\LiveChatController as AdminLiveChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{tag}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{post:slug}/comment', [BlogController::class, 'storeComment'])->name('blog.comment');

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

// Shared Wishlist (public)
Route::get('/wishlist/shared/{token}', [WishlistController::class, 'viewShared'])->name('wishlist.shared');

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

// Product Comparison
Route::get('/compare', [App\Http\Controllers\CompareController::class, 'index'])->name('compare.index');
Route::post('/compare/add/{product:id}', [App\Http\Controllers\CompareController::class, 'add'])->name('compare.add');
Route::post('/compare/remove/{product:id}', [App\Http\Controllers\CompareController::class, 'remove'])->name('compare.remove');
Route::post('/compare/clear', [App\Http\Controllers\CompareController::class, 'clear'])->name('compare.clear');
Route::get('/compare/list', [App\Http\Controllers\CompareController::class, 'getList'])->name('compare.list');

// Recently Viewed
Route::get('/recently-viewed', [App\Http\Controllers\RecentlyViewedController::class, 'index'])->name('recently-viewed.index');
Route::post('/recently-viewed/clear', [App\Http\Controllers\RecentlyViewedController::class, 'clear'])->name('recently-viewed.clear');

// Services
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

// Jobs (Browse)
Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');
Route::get('/jobs/create', [JobPostingController::class, 'create'])->name('jobs.create')->middleware(['auth', 'verified']);
Route::get('/jobs/{jobPosting:slug}', [JobPostingController::class, 'show'])->name('jobs.show');

// Product Requests (public form)
Route::get('/request-product', [ProductRequestController::class, 'create'])->name('product-request.create');
Route::post('/request-product', [ProductRequestController::class, 'store'])->name('product-request.store');
Route::get('/request-product/success', [ProductRequestController::class, 'success'])->name('product-request.success');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');

// Email Tracking
Route::get('/email/track/open/{hash}', [App\Http\Controllers\EmailTrackingController::class, 'trackOpen'])->name('email.track.open');
Route::get('/email/track/click/{hash}', [App\Http\Controllers\EmailTrackingController::class, 'trackClick'])->name('email.track.click');

// Stripe Webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])->name('stripe.webhook');

// Payoneer Webhook (must be outside auth middleware)
Route::post('/payoneer/webhook', [CheckoutController::class, 'payoneerWebhook'])->name('payoneer.webhook');

// Escrow Webhook
Route::post('/escrow/webhook', [EscrowController::class, 'webhook'])->name('escrow.webhook');

// Social Login
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');

// Live Chat (Public Widget)
Route::prefix('live-chat')->name('live-chat.')->group(function () {
    Route::get('/active', [LiveChatController::class, 'getActiveChat'])->name('active');
    Route::get('/availability', [LiveChatController::class, 'getAvailability'])->name('availability');
    Route::post('/start', [LiveChatController::class, 'start'])->name('start');
    Route::get('/{chat}/messages', [LiveChatController::class, 'getMessages'])->name('messages');
    Route::get('/{chat}/status', [LiveChatController::class, 'getStatus'])->name('status');
    Route::post('/{chat}/send', [LiveChatController::class, 'sendMessage'])->name('send');
    Route::post('/{chat}/end', [LiveChatController::class, 'endChat'])->name('end');
});

// Admin Live Chat Routes
Route::middleware(['auth'])->prefix('admin/live-chat')->name('admin.live-chat.')->group(function () {
    Route::get('/waiting', function () {
        return \App\Models\LiveChat::where('status', 'waiting')
            ->with(['user', 'latestMessage'])
            ->latest()
            ->get();
    })->name('waiting');
    Route::get('/waiting-count', [AdminLiveChatController::class, 'getWaitingCount'])->name('waiting-count');
    Route::get('/quick-responses', [AdminLiveChatController::class, 'getQuickResponses'])->name('quick-responses');
    Route::post('/{chat}/accept', [AdminLiveChatController::class, 'accept'])->name('accept');
    Route::get('/{chat}/messages', [AdminLiveChatController::class, 'getMessages'])->name('messages');
    Route::post('/{chat}/send', [AdminLiveChatController::class, 'sendMessage'])->name('send');
    Route::post('/{chat}/close', [AdminLiveChatController::class, 'close'])->name('close');
    Route::post('/{chat}/transfer', [AdminLiveChatController::class, 'transfer'])->name('transfer');
});

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

    // Invoices
    Route::get('/invoices/{order}/download', [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/invoices/{order}/view', [App\Http\Controllers\InvoiceController::class, 'view'])->name('invoice.view');

    // Wishlist
    Route::get('/wishlist', [DashboardController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist/share-link', [WishlistController::class, 'getShareLink'])->name('wishlist.share-link');
    Route::post('/wishlist/toggle-visibility', [WishlistController::class, 'toggleVisibility'])->name('wishlist.toggle-visibility');
    Route::post('/wishlist/regenerate-link', [WishlistController::class, 'regenerateShareLink'])->name('wishlist.regenerate-link');

    // Referrals
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');

    // Push Notifications
    Route::prefix('push')->name('push.')->group(function () {
        Route::get('/vapid-public-key', [PushSubscriptionController::class, 'getVapidPublicKey'])->name('vapid-key');
        Route::post('/subscribe', [PushSubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('unsubscribe');
        Route::get('/preferences', [PushSubscriptionController::class, 'getPreferences'])->name('preferences');
        Route::put('/preferences', [PushSubscriptionController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/subscriptions', [PushSubscriptionController::class, 'getSubscriptions'])->name('subscriptions');
        Route::delete('/subscriptions/{subscription}', [PushSubscriptionController::class, 'removeSubscription'])->name('subscriptions.remove');
    });

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
    Route::get('/checkout/payoneer/success/{order}', [CheckoutController::class, 'payoneerSuccess'])->name('checkout.payoneer.success');

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

    // Product Requests (user's requests)
    Route::get('/my-requests', [ProductRequestController::class, 'index'])->name('product-request.index');
    Route::get('/my-requests/{productRequest}', [ProductRequestController::class, 'show'])->name('product-request.show');

    // Services (Ordering)
    Route::get('/services/{service:slug}/order/{package}', [ServiceController::class, 'order'])->name('services.order');
    Route::post('/services/{service:slug}/order/{package}', [ServiceController::class, 'processOrder'])->name('services.process-order');

    // Service Orders (Buyer)
    Route::get('/service-orders', [ServiceOrderController::class, 'index'])->name('service-orders.index');
    Route::get('/service-orders/{serviceOrder}', [ServiceOrderController::class, 'show'])->name('service-orders.show');
    Route::post('/service-orders/{serviceOrder}/requirements', [ServiceOrderController::class, 'submitRequirements'])->name('service-orders.submit-requirements');
    Route::post('/service-orders/{serviceOrder}/approve', [ServiceOrderController::class, 'approveDelivery'])->name('service-orders.approve');
    Route::post('/service-orders/{serviceOrder}/revision', [ServiceOrderController::class, 'requestRevision'])->name('service-orders.revision');
    Route::post('/service-orders/{serviceOrder}/cancel', [ServiceOrderController::class, 'cancel'])->name('service-orders.cancel');

    // Custom Quotes
    Route::get('/services/{service:slug}/quote', [CustomQuoteController::class, 'create'])->name('quotes.create');
    Route::post('/services/{service:slug}/quote', [CustomQuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes', [CustomQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quoteRequest}', [CustomQuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{quoteRequest}/accept', [CustomQuoteController::class, 'accept'])->name('quotes.accept');
    Route::post('/quotes/{quoteRequest}/reject', [CustomQuoteController::class, 'reject'])->name('quotes.reject');

    // Jobs (Client) - Note: jobs/create is defined above in public section with auth middleware
    Route::post('/jobs', [JobPostingController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{jobPosting:slug}/edit', [JobPostingController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{jobPosting}', [JobPostingController::class, 'update'])->name('jobs.update');
    Route::get('/my-jobs', [JobPostingController::class, 'myJobs'])->name('jobs.my-jobs');
    Route::get('/jobs/{jobPosting:slug}/proposals', [JobPostingController::class, 'proposals'])->name('jobs.proposals');
    Route::post('/jobs/{jobPosting}/proposals/{proposal}/accept', [JobPostingController::class, 'acceptProposal'])->name('jobs.accept-proposal');
    Route::post('/jobs/{jobPosting}/proposals/{proposal}/reject', [JobPostingController::class, 'rejectProposal'])->name('jobs.reject-proposal');
    Route::post('/jobs/{jobPosting}/close', [JobPostingController::class, 'close'])->name('jobs.close');

    // Contracts
    Route::get('/contracts', [JobContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/{contract}', [JobContractController::class, 'show'])->name('contracts.show');
    Route::post('/milestones/{milestone}/fund', [JobContractController::class, 'fundMilestone'])->name('milestones.fund');
    Route::post('/milestones/{milestone}/approve', [JobContractController::class, 'approveMilestone'])->name('milestones.approve');
    Route::post('/milestones/{milestone}/revision', [JobContractController::class, 'requestMilestoneRevision'])->name('milestones.revision');
    Route::post('/contracts/{contract}/cancel', [JobContractController::class, 'cancel'])->name('contracts.cancel');

    // Escrow Payments
    Route::get('/escrow/service-order/{serviceOrder}', [EscrowController::class, 'checkoutServiceOrder'])->name('escrow.checkout.service-order');
    Route::get('/escrow/milestone/{milestone}', [EscrowController::class, 'checkoutMilestone'])->name('escrow.checkout.milestone');
    Route::post('/escrow/create-payment-intent', [EscrowController::class, 'createPaymentIntent'])->name('escrow.create-payment-intent');
    Route::get('/escrow/confirm', [EscrowController::class, 'confirmPayment'])->name('escrow.confirm');
    Route::get('/escrow/cancel', [EscrowController::class, 'cancelPayment'])->name('escrow.cancel');
    Route::get('/escrow/{transaction}', [EscrowController::class, 'show'])->name('escrow.show');
    Route::post('/escrow/{transaction}/release', [EscrowController::class, 'release'])->name('escrow.release');
    Route::post('/escrow/{transaction}/refund', [EscrowController::class, 'requestRefund'])->name('escrow.refund');
});

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Seller\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/vacation', [App\Http\Controllers\Seller\DashboardController::class, 'updateVacationMode'])->name('vacation.update');
    Route::resource('products', App\Http\Controllers\Seller\ProductController::class);
    Route::get('/orders', [App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
    Route::get('/invoices/{order}/download', [App\Http\Controllers\InvoiceController::class, 'sellerDownload'])->name('invoice.download');
    Route::get('/licenses', [App\Http\Controllers\Seller\LicenseController::class, 'index'])->name('licenses.index');
    Route::get('/reviews', [App\Http\Controllers\Seller\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/respond', [App\Http\Controllers\Seller\ReviewController::class, 'respond'])->name('reviews.respond');
    Route::delete('/reviews/{review}/response', [App\Http\Controllers\Seller\ReviewController::class, 'deleteResponse'])->name('reviews.response.delete');
    Route::get('/payouts', [App\Http\Controllers\Seller\PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/request', [App\Http\Controllers\Seller\PayoutController::class, 'request'])->name('payouts.request');

    // Services
    Route::resource('services', App\Http\Controllers\Seller\ServiceController::class);
    Route::post('/services/{service}/toggle-status', [App\Http\Controllers\Seller\ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

    // Service Orders
    Route::get('/service-orders', [App\Http\Controllers\Seller\ServiceOrderController::class, 'index'])->name('service-orders.index');
    Route::get('/service-orders/{serviceOrder}', [App\Http\Controllers\Seller\ServiceOrderController::class, 'show'])->name('service-orders.show');
    Route::post('/service-orders/{serviceOrder}/start', [App\Http\Controllers\Seller\ServiceOrderController::class, 'start'])->name('service-orders.start');
    Route::post('/service-orders/{serviceOrder}/deliver', [App\Http\Controllers\Seller\ServiceOrderController::class, 'deliver'])->name('service-orders.deliver');
    Route::post('/service-orders/{serviceOrder}/cancel-request', [App\Http\Controllers\Seller\ServiceOrderController::class, 'requestCancellation'])->name('service-orders.cancel-request');
    Route::post('/service-orders/{serviceOrder}/extend', [App\Http\Controllers\Seller\ServiceOrderController::class, 'extendDelivery'])->name('service-orders.extend');

    // Custom Quote Requests
    Route::get('/quote-requests', [App\Http\Controllers\Seller\CustomQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quote-requests/{quoteRequest}', [App\Http\Controllers\Seller\CustomQuoteController::class, 'show'])->name('quotes.show');
    Route::get('/quote-requests/{quoteRequest}/create-quote', [App\Http\Controllers\Seller\CustomQuoteController::class, 'createQuote'])->name('quotes.create-quote');
    Route::post('/quote-requests/{quoteRequest}/quote', [App\Http\Controllers\Seller\CustomQuoteController::class, 'storeQuote'])->name('quotes.store-quote');
    Route::put('/quote-requests/{quoteRequest}/quote', [App\Http\Controllers\Seller\CustomQuoteController::class, 'updateQuote'])->name('quotes.update-quote');
    Route::post('/quote-requests/{quoteRequest}/withdraw', [App\Http\Controllers\Seller\CustomQuoteController::class, 'withdrawQuote'])->name('quotes.withdraw');
    Route::post('/quote-requests/{quoteRequest}/decline', [App\Http\Controllers\Seller\CustomQuoteController::class, 'decline'])->name('quotes.decline');

    // Job Proposals
    Route::get('/available-jobs', [App\Http\Controllers\Seller\JobProposalController::class, 'availableJobs'])->name('jobs.available');
    Route::get('/jobs/{jobPosting}/apply', [App\Http\Controllers\Seller\JobProposalController::class, 'create'])->name('proposals.create');
    Route::post('/jobs/{jobPosting}/apply', [App\Http\Controllers\Seller\JobProposalController::class, 'store'])->name('proposals.store');
    Route::get('/my-proposals', [App\Http\Controllers\Seller\JobProposalController::class, 'index'])->name('proposals.index');
    Route::get('/proposals/{proposal}', [App\Http\Controllers\Seller\JobProposalController::class, 'show'])->name('proposals.show');
    Route::get('/proposals/{proposal}/edit', [App\Http\Controllers\Seller\JobProposalController::class, 'edit'])->name('proposals.edit');
    Route::put('/proposals/{proposal}', [App\Http\Controllers\Seller\JobProposalController::class, 'update'])->name('proposals.update');
    Route::post('/proposals/{proposal}/withdraw', [App\Http\Controllers\Seller\JobProposalController::class, 'withdraw'])->name('proposals.withdraw');

    // Job Contracts (Seller side)
    Route::get('/contracts', [App\Http\Controllers\Seller\JobContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/{contract}', [App\Http\Controllers\Seller\JobContractController::class, 'show'])->name('contracts.show');
    Route::post('/milestones/{milestone}/start', [App\Http\Controllers\Seller\JobContractController::class, 'startMilestone'])->name('milestones.start');
    Route::post('/milestones/{milestone}/submit', [App\Http\Controllers\Seller\JobContractController::class, 'submitMilestone'])->name('milestones.submit');
    Route::post('/contracts/{contract}/extension', [App\Http\Controllers\Seller\JobContractController::class, 'requestExtension'])->name('contracts.extension');

    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Seller\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/sales-data', [App\Http\Controllers\Seller\AnalyticsController::class, 'salesData'])->name('analytics.sales-data');
    Route::get('/analytics/products-data', [App\Http\Controllers\Seller\AnalyticsController::class, 'productsData'])->name('analytics.products-data');
    Route::get('/analytics/export', [App\Http\Controllers\Seller\AnalyticsController::class, 'export'])->name('analytics.export');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
