<x-layouts.app title="FAQ - Codexse">
    <!-- Header -->
    <section class="bg-gradient-to-br from-surface-50 to-white dark:from-surface-950 dark:to-surface-900 py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-surface-900 dark:text-white">Frequently Asked Questions</h1>
            <p class="mt-4 text-lg text-surface-600 dark:text-surface-400">Find answers to common questions about our products and services.</p>

            <!-- Search -->
            <form action="{{ route('faq.index') }}" method="GET" class="mt-8 max-w-xl mx-auto">
                @if($selectedCategory)
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search FAQs..."
                        class="w-full rounded-xl border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 pl-12 pr-4 py-4 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                    >
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if($search)
                        <a href="{{ route('faq.index', $selectedCategory ? ['category' => $selectedCategory] : []) }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>

            @if($search)
                <p class="mt-4 text-surface-600 dark:text-surface-400">
                    Found <strong>{{ $totalCount }}</strong> result{{ $totalCount !== 1 ? 's' : '' }} for "{{ $search }}"
                </p>
            @endif
        </div>
    </section>

    <!-- Category Filter -->
    @if($categories->isNotEmpty())
        <section class="py-6 bg-white dark:bg-surface-900 border-b border-surface-200 dark:border-surface-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap gap-2 justify-center">
                    <a
                        href="{{ route('faq.index', $search ? ['q' => $search] : []) }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !$selectedCategory ? 'bg-primary-600 text-white' : 'bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-700' }}"
                    >
                        All
                    </a>
                    @foreach($categories as $cat)
                        <a
                            href="{{ route('faq.index', array_filter(['category' => $cat, 'q' => $search])) }}"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $selectedCategory === $cat ? 'bg-primary-600 text-white' : 'bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-700' }}"
                        >
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- FAQs Content -->
    <section class="py-16 bg-white dark:bg-surface-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-4">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    @forelse($groupedFaqs as $category => $faqs)
                        <div class="mb-12 last:mb-0">
                            <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-6 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </span>
                                {{ $category }}
                                <span class="text-sm font-normal text-surface-500">({{ $faqs->count() }})</span>
                            </h2>

                            <div class="space-y-4" x-data="{ open: null }">
                                @foreach($faqs as $faq)
                                    <div class="bg-surface-50 dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                                        <button
                                            @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}"
                                            class="flex items-center justify-between w-full px-6 py-4 text-left"
                                        >
                                            <span class="font-medium text-surface-900 dark:text-white pr-4">{{ $faq->question }}</span>
                                            <svg class="w-5 h-5 flex-shrink-0 text-surface-500 transition-transform" :class="open === {{ $faq->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div x-show="open === {{ $faq->id }}" x-collapse x-cloak>
                                            <div class="px-6 pb-4 text-surface-600 dark:text-surface-400 prose dark:prose-invert max-w-none">
                                                {!! clean($faq->answer) !!}
                                            </div>
                                            @if($faq->keywords)
                                                <div class="px-6 pb-4 flex flex-wrap gap-2">
                                                    @foreach($faq->keywords_array as $keyword)
                                                        <a
                                                            href="{{ route('faq.index', ['q' => $keyword]) }}"
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs bg-surface-200 dark:bg-surface-700 text-surface-600 dark:text-surface-400 hover:bg-primary-100 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                                        >
                                                            {{ $keyword }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto w-16 h-16 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-surface-900 dark:text-white">No FAQs found</h3>
                            <p class="mt-2 text-surface-600 dark:text-surface-400">
                                @if($search)
                                    Try a different search term or <a href="{{ route('faq.index') }}" class="text-primary-600 hover:underline">browse all FAQs</a>.
                                @else
                                    FAQs will appear here once they are added.
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    @if($popularFaqs->isNotEmpty())
                        <div class="bg-surface-50 dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 sticky top-24">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.56 21a1 1 0 01-.46-.11L12 18.22l-5.1 2.67a1 1 0 01-1.45-1.06l1-5.63-4.12-4a1 1 0 01-.25-1 1 1 0 01.81-.68l5.7-.83 2.51-5.13a1 1 0 011.8 0l2.54 5.12 5.7.83a1 1 0 01.81.68 1 1 0 01-.25 1l-4.12 4 1 5.63a1 1 0 01-.4 1 1 1 0 01-.62.21z"/>
                                </svg>
                                Popular Questions
                            </h3>
                            <ul class="space-y-3">
                                @foreach($popularFaqs as $popularFaq)
                                    <li>
                                        <a
                                            href="{{ route('faq.index', ['q' => $popularFaq->question]) }}"
                                            class="text-sm text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors line-clamp-2"
                                        >
                                            {{ $popularFaq->question }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Chat Widget CTA -->
                    <div class="mt-6 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-6 text-white">
                        <h3 class="font-semibold mb-2">Can't find your answer?</h3>
                        <p class="text-sm text-primary-100 mb-4">Chat with our support bot for instant help.</p>
                        <button
                            onclick="window.dispatchEvent(new CustomEvent('open-chatbot'))"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-white text-primary-600 rounded-lg font-medium hover:bg-primary-50 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Start Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-16 bg-surface-50 dark:bg-surface-800/50">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-surface-900 dark:text-white mb-4">Still Need Help?</h2>
            <p class="text-surface-600 dark:text-surface-400 mb-8">Our support team is ready to assist you with any questions.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white hover:bg-primary-700 transition-colors">
                    Contact Support
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
                @auth
                    <a href="{{ route('support.create') }}" class="inline-flex items-center justify-center rounded-xl border-2 border-surface-300 dark:border-surface-600 px-6 py-3 text-base font-semibold text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors">
                        <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        Submit Ticket
                    </a>
                @endauth
            </div>
        </div>
    </section>
</x-layouts.app>
