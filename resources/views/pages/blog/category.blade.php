<x-layouts.app
    :title="$category->name . ' - Blog - ' . config('app.name')"
    :description="$category->description ?: 'Browse articles in ' . $category->name"
>
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-accent-50 dark:from-surface-900 dark:via-surface-900 dark:to-surface-800">
        <div class="absolute top-0 left-0 w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 translate-x-1/2 -translate-y-1/2"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center gap-2 text-sm text-surface-500 dark:text-surface-400">
                    <li><a href="{{ url('/') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a></li>
                    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Blog</a></li>
                    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
                    <li class="text-surface-900 dark:text-white font-medium">{{ $category->name }}</li>
                </ol>
            </nav>

            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-full mb-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="font-semibold">Category</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-surface-900 dark:text-white">{{ $category->name }}</h1>
                @if($category->description)
                <p class="mt-4 text-lg text-surface-600 dark:text-surface-400 leading-relaxed">
                    {{ $category->description }}
                </p>
                @endif
                <p class="mt-6 text-surface-500 dark:text-surface-400">
                    {{ $posts->total() }} {{ Str::plural('article', $posts->total()) }} in this category
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-3">
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
                                <span class="text-xs text-surface-500">{{ $post->published_at->format('M d, Y') }}</span>
                                <span class="text-surface-300 dark:text-surface-600">&middot;</span>
                                <span class="text-xs text-surface-500">{{ $post->reading_time }} min read</span>
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
                                <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $post->author->name }}</span>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
                @else
                <div class="text-center py-20 bg-surface-50 dark:bg-surface-800/50 rounded-3xl">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                        <svg class="w-10 h-10 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">No articles in this category</h3>
                    <p class="text-surface-500 dark:text-surface-400 mb-6">Check back later for new content.</p>
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        View all posts
                    </a>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="lg:col-span-1 space-y-8">
                <!-- Categories -->
                @if($categories->isNotEmpty())
                <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-lg shadow-surface-200/50 dark:shadow-none border border-surface-100 dark:border-surface-700">
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-5">Categories</h3>
                    <div class="space-y-2">
                        @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat) }}" class="flex items-center justify-between p-3 rounded-xl transition-colors {{ $cat->id === $category->id ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'hover:bg-surface-50 dark:hover:bg-surface-700 text-surface-700 dark:text-surface-300 hover:text-primary-600 dark:hover:text-primary-400' }} group">
                            <span class="font-medium">{{ $cat->name }}</span>
                            <span class="text-sm {{ $cat->id === $category->id ? 'bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400' : 'bg-surface-100 dark:bg-surface-700 text-surface-500' }} px-2.5 py-1 rounded-full">{{ $cat->published_posts_count }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Popular Tags -->
                @if(!empty($popularTags))
                <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-lg shadow-surface-200/50 dark:shadow-none border border-surface-100 dark:border-surface-700">
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

                <!-- Back to Blog -->
                <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl p-6 text-white">
                    <h3 class="text-lg font-bold mb-2">Explore More</h3>
                    <p class="text-primary-100 text-sm mb-4">Discover more articles across all categories.</p>
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        All Articles
                    </a>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
