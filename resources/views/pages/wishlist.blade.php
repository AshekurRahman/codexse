<x-layouts.app title="Wishlist - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Wishlist</h1>
                        <p class="mt-1 text-surface-600 dark:text-surface-400">Products you've saved for later</p>
                    </div>

                    <!-- Share Wishlist Section -->
                    <div class="flex items-center gap-3">
                        <button
                            id="share-wishlist-btn"
                            onclick="openShareModal()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 font-medium rounded-xl hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-sm"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            Share Wishlist
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <nav class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-b border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Overview</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Products</p>
                        </div>
                        <a href="{{ route('purchases') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            My Purchases
                        </a>
                        <a href="{{ route('wishlist') }}" class="flex items-center gap-3 px-4 py-3 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-4 border-primary-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Wishlist
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Services</p>
                        </div>
                        <a href="{{ route('service-orders.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Service Orders
                        </a>
                        <a href="{{ route('quotes.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Quote Requests
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Jobs</p>
                        </div>
                        <a href="{{ route('jobs.my-jobs') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            My Job Posts
                        </a>
                        <a href="{{ route('contracts.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Contracts
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Communication</p>
                        </div>
                        <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Messages
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Account</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>

                        @if(!auth()->user()->seller)
                            <a href="{{ route('become-seller') }}" class="flex items-center gap-3 px-4 py-3 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 border-l-4 border-transparent transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Become a Seller
                            </a>
                        @endif
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    @if($wishlists->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($wishlists as $item)
                                <x-product-card :product="$item->product" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $wishlists->links() }}
                        </div>
                    @else
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-6 py-16 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">Your wishlist is empty</h3>
                            <p class="text-surface-600 dark:text-surface-400 mb-6">Save products you like by clicking the heart icon</p>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                Browse Products
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Share Wishlist Modal -->
    <div id="share-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeShareModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-surface-800 rounded-2xl shadow-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white">Share Your Wishlist</h3>
                    <button onclick="closeShareModal()" class="text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Visibility Toggle -->
                <div class="mb-6">
                    <label class="flex items-center justify-between p-4 bg-surface-50 dark:bg-surface-700/50 rounded-xl cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div id="visibility-icon" class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-surface-900 dark:text-white">Public Wishlist</p>
                                <p id="visibility-text" class="text-sm text-surface-500 dark:text-surface-400">Anyone with the link can view</p>
                            </div>
                        </div>
                        <button
                            id="visibility-toggle"
                            onclick="toggleVisibility()"
                            class="relative w-12 h-7 bg-surface-300 dark:bg-surface-600 rounded-full transition-colors"
                        >
                            <span id="toggle-dot" class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full transition-transform shadow"></span>
                        </button>
                    </label>
                </div>

                <!-- Share Link -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Share Link</label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            id="share-link-input"
                            readonly
                            class="flex-1 px-4 py-2.5 bg-surface-50 dark:bg-surface-700 border border-surface-200 dark:border-surface-600 rounded-xl text-surface-900 dark:text-white text-sm"
                            value=""
                        >
                        <button
                            onclick="copyShareLink()"
                            class="px-4 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Regenerate Link -->
                <div class="flex items-center justify-between pt-4 border-t border-surface-200 dark:border-surface-700">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Need a new link?</p>
                    <button
                        onclick="regenerateLink()"
                        class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                    >
                        Regenerate Link
                    </button>
                </div>

                <!-- Social Share -->
                <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                    <p class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">Share on social media</p>
                    <div class="flex items-center gap-3">
                        <button onclick="shareOnTwitter()" class="w-10 h-10 bg-[#1DA1F2] text-white rounded-full flex items-center justify-center hover:opacity-90 transition-opacity">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </button>
                        <button onclick="shareOnFacebook()" class="w-10 h-10 bg-[#1877F2] text-white rounded-full flex items-center justify-center hover:opacity-90 transition-opacity">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </button>
                        <button onclick="shareOnWhatsApp()" class="w-10 h-10 bg-[#25D366] text-white rounded-full flex items-center justify-center hover:opacity-90 transition-opacity">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </button>
                        <button onclick="shareByEmail()" class="w-10 h-10 bg-surface-600 text-white rounded-full flex items-center justify-center hover:opacity-90 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 transform translate-y-full opacity-0 transition-all duration-300 z-50">
        <div class="flex items-center gap-3 px-4 py-3 bg-surface-900 dark:bg-surface-700 text-white rounded-xl shadow-lg">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span id="toast-message">Link copied to clipboard!</span>
        </div>
    </div>

    @push('scripts')
    <script>
        let isPublic = false;
        let shareUrl = '';

        function openShareModal() {
            // Fetch share link data
            fetch('{{ route('wishlist.share-link') }}')
                .then(response => response.json())
                .then(data => {
                    shareUrl = data.share_url;
                    isPublic = data.is_public;
                    document.getElementById('share-link-input').value = shareUrl;
                    updateVisibilityUI();
                    document.getElementById('share-modal').classList.remove('hidden');
                });
        }

        function closeShareModal() {
            document.getElementById('share-modal').classList.add('hidden');
        }

        function updateVisibilityUI() {
            const toggle = document.getElementById('visibility-toggle');
            const dot = document.getElementById('toggle-dot');
            const text = document.getElementById('visibility-text');

            if (isPublic) {
                toggle.classList.remove('bg-surface-300', 'dark:bg-surface-600');
                toggle.classList.add('bg-primary-600');
                dot.classList.add('translate-x-5');
                text.textContent = 'Anyone with the link can view';
            } else {
                toggle.classList.add('bg-surface-300', 'dark:bg-surface-600');
                toggle.classList.remove('bg-primary-600');
                dot.classList.remove('translate-x-5');
                text.textContent = 'Only you can see your wishlist';
            }
        }

        function toggleVisibility() {
            fetch('{{ route('wishlist.toggle-visibility') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                isPublic = data.is_public;
                updateVisibilityUI();
                showToast(data.message);
            });
        }

        function copyShareLink() {
            const input = document.getElementById('share-link-input');
            navigator.clipboard.writeText(input.value).then(() => {
                showToast('Link copied to clipboard!');
            }).catch(() => {
                input.select();
                document.execCommand('copy');
                showToast('Link copied to clipboard!');
            });
        }

        function regenerateLink() {
            fetch('{{ route('wishlist.regenerate-link') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                shareUrl = data.share_url;
                document.getElementById('share-link-input').value = shareUrl;
                showToast(data.message);
            });
        }

        function shareOnTwitter() {
            const text = encodeURIComponent('Check out my wishlist!');
            const url = encodeURIComponent(shareUrl);
            window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
        }

        function shareOnFacebook() {
            const url = encodeURIComponent(shareUrl);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
        }

        function shareOnWhatsApp() {
            const text = encodeURIComponent('Check out my wishlist! ' + shareUrl);
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }

        function shareByEmail() {
            const subject = encodeURIComponent('Check out my wishlist!');
            const body = encodeURIComponent('I wanted to share my wishlist with you: ' + shareUrl);
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;
            toast.classList.remove('translate-y-full', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
            }, 3000);
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeShareModal();
            }
        });
    </script>
    @endpush
</x-layouts.app>
