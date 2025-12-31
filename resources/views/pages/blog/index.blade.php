<x-layouts.app
    title="Blog - {{ config('app.name') }}"
    description="Read our latest articles, tutorials, and insights about digital products, design, and development."
>
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-accent-50 dark:from-surface-900 dark:via-surface-900 dark:to-surface-800">
        <div class="absolute top-0 left-0 w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 translate-x-1/2 -translate-y-1/2"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-surface-900 dark:text-white tracking-tight">
                    Our <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-600">Blog</span>
                </h1>
                <p class="mt-6 text-lg md:text-xl text-surface-600 dark:text-surface-400 leading-relaxed">
                    Discover tutorials, design tips, industry insights, and success stories from creators around the world.
                </p>

                <!-- Search -->
                <div class="mt-10 max-w-xl mx-auto">
                    <form action="{{ route('blog.index') }}" method="GET">
                        <div class="relative group">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search articles..."
                                class="w-full pl-14 pr-6 py-4 bg-white dark:bg-surface-800 border-2 border-surface-200 dark:border-surface-700 rounded-2xl text-surface-900 dark:text-white placeholder-surface-400 shadow-lg shadow-surface-200/50 dark:shadow-none focus:outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all"
                            >
                            <div class="absolute left-5 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-surface-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Category Pills -->
            @if($categories->isNotEmpty())
            <div class="mt-12 flex flex-wrap justify-center gap-3">
                <a href="{{ route('blog.index') }}" class="px-5 py-2.5 text-sm font-semibold rounded-full transition-all {{ !request('category') && !request('tag') && !request('search') ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white dark:bg-surface-800 text-surface-700 dark:text-surface-300 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    All Posts
                </a>
                @foreach($categories as $category)
                <a href="{{ route('blog.category', $category) }}" class="px-5 py-2.5 text-sm font-semibold rounded-full bg-white dark:bg-surface-800 text-surface-700 dark:text-surface-300 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Search Results Header -->
        @if(request()->has('search'))
        <div class="mb-10 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-surface-900 dark:text-white">
                    Results for "{{ request('search') }}"
                </h2>
                <p class="mt-1 text-surface-500">{{ $posts->total() }} {{ Str::plural('article', $posts->total()) }} found</p>
            </div>
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Clear search
            </a>
        </div>
        @endif

        <!-- Featured Posts -->
        @if($featuredPosts->isNotEmpty() && !request()->has('category') && !request()->has('tag') && !request()->has('search'))
        <section class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-surface-900 dark:text-white">Featured Articles</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($featuredPosts->take(1) as $post)
                <!-- Main Featured -->
                <article class="group relative bg-white dark:bg-surface-800 rounded-3xl overflow-hidden shadow-xl shadow-surface-200/50 dark:shadow-none">
                    <a href="{{ route('blog.show', $post) }}" class="block">
                        <div class="aspect-[4/3] overflow-hidden">
                            @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                            <div class="w-full h-full bg-gradient-to-br from-primary-500 to-accent-600 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>
                            @endif
                        </div>
                    </a>
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-4">
                            @if($post->category)
                            <a href="{{ route('blog.category', $post->category) }}" class="px-3 py-1 text-xs font-semibold bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-full">
                                {{ $post->category->name }}
                            </a>
                            @endif
                            <span class="text-sm text-surface-500">{{ $post->published_at->format('M d, Y') }}</span>
                        </div>
                        <h3 class="text-2xl font-bold text-surface-900 dark:text-white mb-3">
                            <a href="{{ route('blog.show', $post) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                {{ $post->title }}
                            </a>
                        </h3>
                        <p class="text-surface-600 dark:text-surface-400 line-clamp-2 mb-6">
                            {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 150) }}
                        </p>
                        <div class="flex items-center gap-3">
                            @if($post->author->profile_photo_path)
                            <img src="{{ asset('storage/' . $post->author->profile_photo_path) }}" alt="{{ $post->author->name }}" class="w-10 h-10 rounded-full">
                            @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-bold">
                                {{ substr($post->author->name, 0, 1) }}
                            </div>
                            @endif
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white text-sm">{{ $post->author->name }}</p>
                                <p class="text-xs text-surface-500">{{ $post->reading_time }} min read</p>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach

                <!-- Secondary Featured -->
                <div class="space-y-6">
                    @foreach($featuredPosts->skip(1)->take(2) as $post)
                    <article class="group flex gap-6 bg-white dark:bg-surface-800 rounded-2xl p-4 shadow-lg shadow-surface-200/50 dark:shadow-none">
                        <a href="{{ route('blog.show', $post) }}" class="flex-shrink-0">
                            <div class="w-40 h-32 rounded-xl overflow-hidden">
                                @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-500 to-accent-600 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                        </a>
                        <div class="flex-1 min-w-0 py-1">
                            @if($post->category)
                            <a href="{{ route('blog.category', $post->category) }}" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                                {{ $post->category->name }}
                            </a>
                            @endif
                            <h3 class="mt-2 text-lg font-bold text-surface-900 dark:text-white line-clamp-2">
                                <a href="{{ route('blog.show', $post) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <div class="mt-3 flex items-center gap-3 text-sm text-surface-500">
                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                                <span>&middot;</span>
                                <span>{{ $post->reading_time }} min read</span>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-surface-900 dark:text-white">
                        {{ request()->has('search') ? '' : 'Latest Articles' }}
                    </h2>
                </div>

                @if($posts->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($posts as $post)
                    <article class="group bg-white dark:bg-surface-800 rounded-2xl overflow-hidden shadow-lg shadow-surface-200/50 dark:shadow-none hover:shadow-xl transition-shadow">
                        <a href="{{ route('blog.show', $post) }}" class="block">
                            <div class="aspect-video overflow-hidden">
                                @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                <div class="w-full h-full bg-gradient-to-br from-surface-100 to-surface-200 dark:from-surface-700 dark:to-surface-600 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                        </a>
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-3">
                                @if($post->category)
                                <a href="{{ route('blog.category', $post->category) }}" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                                    {{ $post->category->name }}
                                </a>
                                <span class="text-surface-300 dark:text-surface-600">&middot;</span>
                                @endif
                                <span class="text-xs text-surface-500">{{ $post->published_at->format('M d, Y') }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-surface-900 dark:text-white line-clamp-2 mb-3">
                                <a href="{{ route('blog.show', $post) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-surface-600 dark:text-surface-400 line-clamp-2 mb-4">
                                {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 100) }}
                            </p>
                            <div class="flex items-center gap-3">
                                @if($post->author->profile_photo_path)
                                <img src="{{ asset('storage/' . $post->author->profile_photo_path) }}" alt="{{ $post->author->name }}" class="w-8 h-8 rounded-full">
                                @else
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-bold text-xs">
                                    {{ substr($post->author->name, 0, 1) }}
                                </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-surface-900 dark:text-white truncate">{{ $post->author->name }}</p>
                                </div>
                                <span class="text-xs text-surface-500">{{ $post->reading_time }} min</span>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->withQueryString()->links() }}
                </div>
                @else
                <div class="text-center py-20 bg-surface-50 dark:bg-surface-800/50 rounded-3xl">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                        <svg class="w-10 h-10 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">No articles found</h3>
                    <p class="text-surface-500 dark:text-surface-400">Check back later for new content.</p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="lg:col-span-1 space-y-8">
                <!-- Newsletter -->
                <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl p-6 text-white">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Subscribe to Newsletter</h3>
                    <p class="text-primary-100 text-sm mb-5">Get the latest posts delivered right to your inbox.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="email" name="email" placeholder="Enter your email" required class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-xl text-white placeholder-primary-200 focus:outline-none focus:ring-2 focus:ring-white/50 mb-3">
                        <button type="submit" class="w-full py-3 bg-white text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-colors">
                            Subscribe
                        </button>
                    </form>
                </div>

                <!-- Categories -->
                @if($categories->isNotEmpty())
                <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-lg shadow-surface-200/50 dark:shadow-none">
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-5">Categories</h3>
                    <div class="space-y-3">
                        @foreach($categories as $category)
                        <a href="{{ route('blog.category', $category) }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors group">
                            <span class="font-medium text-surface-700 dark:text-surface-300 group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $category->name }}</span>
                            <span class="text-sm bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400 px-3 py-1 rounded-full">{{ $category->published_posts_count }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Popular Tags -->
                @if(!empty($popularTags))
                <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-lg shadow-surface-200/50 dark:shadow-none">
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-5">Popular Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($popularTags as $tag => $count)
                        <a href="{{ route('blog.tag', $tag) }}" class="px-4 py-2 text-sm font-medium bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-full hover:bg-primary-100 hover:text-primary-700 dark:hover:bg-primary-900 dark:hover:text-primary-300 transition-colors">
                            #{{ $tag }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>
</x-layouts.app>
