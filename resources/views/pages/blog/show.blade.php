<x-layouts.app
    :title="$post->meta_title"
    :description="$post->meta_description"
    :keywords="$post->meta_keywords"
    :image="$post->og_image_url"
    type="article"
    :breadcrumbs="[
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Blog', 'url' => route('blog.index')],
        ['name' => $post->title, 'url' => route('blog.show', $post)],
    ]"
>
    <article>
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-accent-50 dark:from-surface-900 dark:via-surface-900 dark:to-surface-800">
            <div class="absolute top-0 left-0 w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-10 translate-x-1/2 -translate-y-1/2"></div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
                <!-- Breadcrumb -->
                <nav class="mb-8">
                    <ol class="flex items-center gap-2 text-sm text-surface-500 dark:text-surface-400">
                        <li><a href="{{ url('/') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a></li>
                        <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Blog</a></li>
                        @if($post->category)
                        <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
                        <li><a href="{{ route('blog.category', $post->category) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">{{ $post->category->name }}</a></li>
                        @endif
                    </ol>
                </nav>

                @if($post->category)
                <a href="{{ route('blog.category', $post->category) }}" class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 text-sm font-semibold rounded-full mb-6 hover:bg-primary-200 dark:hover:bg-primary-900 transition-colors">
                    {{ $post->category->name }}
                </a>
                @endif

                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-surface-900 dark:text-white leading-tight">
                    {{ $post->title }}
                </h1>

                <div class="mt-8 flex flex-wrap items-center gap-6">
                    <div class="flex items-center gap-3">
                        @if($post->author->profile_photo_path)
                        <img src="{{ asset('storage/' . $post->author->profile_photo_path) }}" alt="{{ $post->author->name }}" class="w-12 h-12 rounded-full ring-4 ring-white dark:ring-surface-700 shadow-lg">
                        @else
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-bold text-lg ring-4 ring-white dark:ring-surface-700 shadow-lg">
                            {{ substr($post->author->name, 0, 1) }}
                        </div>
                        @endif
                        <div>
                            <p class="font-semibold text-surface-900 dark:text-white">{{ $post->author->name }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ $post->published_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-surface-500 dark:text-surface-400">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $post->reading_time }} min read
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ number_format($post->views_count) }} views
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Image -->
        @if($post->featured_image)
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full rounded-3xl shadow-2xl">
        </div>
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
                <!-- Content -->
                <div class="lg:col-span-3">
                    <!-- Article Content -->
                    <div class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-bold prose-a:text-primary-600 dark:prose-a:text-primary-400 prose-img:rounded-2xl">
                        {!! $post->content !!}
                    </div>

                    <!-- Tags -->
                    @if(!empty($post->tags))
                    <div class="mt-12 pt-8 border-t border-surface-200 dark:border-surface-700">
                        <h3 class="text-sm font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-4">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                            <a href="{{ route('blog.tag', $tag) }}" class="px-4 py-2 text-sm font-medium bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 rounded-full hover:bg-primary-100 hover:text-primary-700 dark:hover:bg-primary-900 dark:hover:text-primary-300 transition-colors">
                                #{{ $tag }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Share -->
                    <div class="mt-8 pt-8 border-t border-surface-200 dark:border-surface-700">
                        <h3 class="text-sm font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-4">Share this article</h3>
                        <div class="flex gap-3">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post)) }}&text={{ urlencode($post->title) }}" target="_blank" class="w-12 h-12 flex items-center justify-center bg-surface-100 dark:bg-surface-800 rounded-xl hover:bg-[#1DA1F2] hover:text-white text-surface-600 dark:text-surface-400 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post)) }}" target="_blank" class="w-12 h-12 flex items-center justify-center bg-surface-100 dark:bg-surface-800 rounded-xl hover:bg-[#1877F2] hover:text-white text-surface-600 dark:text-surface-400 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.show', $post)) }}&title={{ urlencode($post->title) }}" target="_blank" class="w-12 h-12 flex items-center justify-center bg-surface-100 dark:bg-surface-800 rounded-xl hover:bg-[#0A66C2] hover:text-white text-surface-600 dark:text-surface-400 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path></svg>
                            </a>
                            <button onclick="navigator.clipboard.writeText('{{ route('blog.show', $post) }}'); this.innerHTML='<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'></path></svg>'; setTimeout(() => this.innerHTML='<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\'></path></svg>', 2000)" class="w-12 h-12 flex items-center justify-center bg-surface-100 dark:bg-surface-800 rounded-xl hover:bg-primary-600 hover:text-white text-surface-600 dark:text-surface-400 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    @if($post->allow_comments)
                    <div class="mt-12 pt-8 border-t border-surface-200 dark:border-surface-700">
                        <div class="flex items-center gap-4 mb-8">
                            <h2 class="text-2xl font-bold text-surface-900 dark:text-white">Comments</h2>
                            <span class="px-3 py-1 text-sm font-medium bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-400 rounded-full">
                                {{ $post->approvedComments->count() }}
                            </span>
                        </div>

                        <!-- Comment Form -->
                        <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 mb-8">
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Leave a Comment</h3>

                            @if(session('success'))
                            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ session('success') }}
                            </div>
                            @endif

                            <form action="{{ route('blog.comment', $post) }}" method="POST">
                                @csrf

                                @guest
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Name</label>
                                        <input type="text" name="author_name" required placeholder="Your name" class="w-full px-4 py-3 border border-surface-200 dark:border-surface-600 rounded-xl bg-surface-50 dark:bg-surface-700 text-surface-900 dark:text-white placeholder-surface-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Email</label>
                                        <input type="email" name="author_email" required placeholder="Your email" class="w-full px-4 py-3 border border-surface-200 dark:border-surface-600 rounded-xl bg-surface-50 dark:bg-surface-700 text-surface-900 dark:text-white placeholder-surface-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                                    </div>
                                </div>
                                @else
                                <div class="flex items-center gap-3 mb-4 p-3 bg-surface-50 dark:bg-surface-700 rounded-xl">
                                    @if(auth()->user()->profile_photo_path)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full">
                                    @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-surface-900 dark:text-white">{{ auth()->user()->name }}</p>
                                        <p class="text-sm text-surface-500">Commenting as</p>
                                    </div>
                                </div>
                                @endguest

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Comment</label>
                                    <textarea name="content" rows="4" required placeholder="Share your thoughts..." class="w-full px-4 py-3 border border-surface-200 dark:border-surface-600 rounded-xl bg-surface-50 dark:bg-surface-700 text-surface-900 dark:text-white placeholder-surface-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-none"></textarea>
                                </div>

                                <div class="flex items-center justify-between">
                                    <p class="text-xs text-surface-500">Comments are moderated before publishing.</p>
                                    <button type="submit" class="px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
                                        Post Comment
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Comments List -->
                        @if($post->approvedComments->isNotEmpty())
                        <div class="space-y-6">
                            @foreach($post->approvedComments as $comment)
                            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                                <div class="flex items-start gap-4">
                                    @if($comment->author_avatar)
                                    <img src="{{ $comment->author_avatar }}" alt="{{ $comment->author_display_name }}" class="w-12 h-12 rounded-full">
                                    @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-400 to-accent-500 flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($comment->author_display_name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-3 mb-2">
                                            <span class="font-semibold text-surface-900 dark:text-white">{{ $comment->author_display_name }}</span>
                                            <span class="text-sm text-surface-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-surface-700 dark:text-surface-300 leading-relaxed">{{ $comment->content }}</p>

                                        @if($comment->replies->isNotEmpty())
                                        <div class="mt-6 space-y-4 pl-6 border-l-2 border-surface-200 dark:border-surface-700">
                                            @foreach($comment->replies as $reply)
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 rounded-full bg-surface-200 dark:bg-surface-700 flex items-center justify-center text-surface-600 dark:text-surface-400 font-semibold text-sm">
                                                    {{ strtoupper(substr($reply->author_display_name, 0, 1)) }}
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                                        <span class="font-medium text-surface-900 dark:text-white text-sm">{{ $reply->author_display_name }}</span>
                                                        <span class="text-xs text-surface-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-sm text-surface-700 dark:text-surface-300">{{ $reply->content }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-12 bg-surface-50 dark:bg-surface-800/50 rounded-2xl">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                                <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-1">No comments yet</h3>
                            <p class="text-surface-500">Be the first to share your thoughts!</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <aside class="lg:col-span-1 space-y-8">
                    <!-- Author -->
                    <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-lg shadow-surface-200/50 dark:shadow-none border border-surface-100 dark:border-surface-700">
                        <h3 class="text-sm font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-4">About the Author</h3>
                        <div class="flex items-center gap-4">
                            @if($post->author->profile_photo_path)
                            <img src="{{ asset('storage/' . $post->author->profile_photo_path) }}" alt="{{ $post->author->name }}" class="w-16 h-16 rounded-full">
                            @else
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-bold text-2xl">
                                {{ substr($post->author->name, 0, 1) }}
                            </div>
                            @endif
                            <div>
                                <p class="font-bold text-surface-900 dark:text-white">{{ $post->author->name }}</p>
                                <p class="text-sm text-surface-500">Author</p>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    @if($categories->isNotEmpty())
                    <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-lg shadow-surface-200/50 dark:shadow-none border border-surface-100 dark:border-surface-700">
                        <h3 class="text-sm font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-4">Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                            <a href="{{ route('blog.category', $category) }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors group">
                                <span class="font-medium text-surface-700 dark:text-surface-300 group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $category->name }}</span>
                                <span class="text-sm bg-surface-100 dark:bg-surface-700 text-surface-500 px-2.5 py-1 rounded-full">{{ $category->published_posts_count }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Related Posts -->
                    @if($relatedPosts->isNotEmpty())
                    <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-lg shadow-surface-200/50 dark:shadow-none border border-surface-100 dark:border-surface-700">
                        <h3 class="text-sm font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-4">Related Articles</h3>
                        <div class="space-y-4">
                            @foreach($relatedPosts as $relatedPost)
                            <a href="{{ route('blog.show', $relatedPost) }}" class="block group">
                                <div class="flex gap-4">
                                    @if($relatedPost->featured_image)
                                    <img src="{{ asset('storage/' . $relatedPost->featured_image) }}" alt="{{ $relatedPost->title }}" class="w-20 h-16 object-cover rounded-lg flex-shrink-0">
                                    @else
                                    <div class="w-20 h-16 bg-gradient-to-br from-surface-100 to-surface-200 dark:from-surface-700 dark:to-surface-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 line-clamp-2 transition-colors">
                                            {{ $relatedPost->title }}
                                        </h4>
                                        <p class="text-xs text-surface-500 mt-1">{{ $relatedPost->published_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </aside>
            </div>
        </div>
    </article>

    <!-- Article Schema -->
    @push('head')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "{{ $post->title }}",
        "description": "{{ $post->meta_description }}",
        "image": "{{ $post->og_image_url }}",
        "author": {
            "@type": "Person",
            "name": "{{ $post->author->name }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ config('app.name') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/logo.png') }}"
            }
        },
        "datePublished": "{{ $post->published_at->toIso8601String() }}",
        "dateModified": "{{ $post->updated_at->toIso8601String() }}",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ route('blog.show', $post) }}"
        }
    }
    </script>
    @endpush
</x-layouts.app>
