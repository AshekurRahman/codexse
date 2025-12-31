<x-layouts.app title="Find Freelance Work | Job Opportunities">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <!-- Hero Section -->
        <div class="relative bg-gradient-to-br from-accent-50 via-purple-50 to-pink-100 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-gradient-to-br from-accent-200/40 to-purple-200/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-gradient-to-br from-pink-200/40 to-rose-200/30 rounded-full blur-3xl"></div>
                <div class="absolute top-1/3 right-1/3 w-64 h-64 bg-gradient-to-br from-purple-200/30 to-accent-200/30 rounded-full blur-3xl"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M40 0L0 0 0 40\" fill=\"none\" stroke=\"%23a855f7\" stroke-opacity=\"0.04\" stroke-width=\"0.5\"/%3E%3C/svg%3E')]"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
                <div class="text-center max-w-3xl mx-auto">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-accent-200 shadow-sm mb-6">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-sm font-medium text-surface-700">{{ number_format($jobs->total()) }} Jobs Available</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl font-bold text-surface-900 mb-4 tracking-tight">
                        Find Your Next <span class="bg-gradient-to-r from-accent-600 to-purple-600 bg-clip-text text-transparent">Freelance</span> Opportunity
                    </h1>
                    <p class="text-lg text-surface-600 mb-8 max-w-2xl mx-auto">
                        Browse thousands of job postings from clients worldwide. Submit proposals, win contracts, and grow your career with secure milestone payments.
                    </p>

                    <!-- Search Box -->
                    <form action="{{ route('jobs.index') }}" method="GET" class="max-w-2xl mx-auto mb-6">
                        <div class="relative">
                            <div class="flex flex-col sm:flex-row gap-3 bg-white/90 backdrop-blur-sm border border-surface-200 shadow-xl shadow-accent-200/30 rounded-2xl p-2">
                                <div class="relative flex-1">
                                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Search jobs by title, skills, or keywords..."
                                        class="w-full rounded-xl border-0 bg-surface-50 pl-12 pr-4 py-3.5 text-surface-900 placeholder-surface-500 focus:ring-2 focus:ring-accent-500 focus:bg-white transition-colors">
                                </div>
                                <button type="submit" class="rounded-xl bg-gradient-to-r from-accent-600 to-accent-700 px-8 py-3.5 font-semibold text-white shadow-lg shadow-accent-500/30 hover:shadow-xl transition-all hover:scale-105">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Popular Searches -->
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <span class="text-sm text-surface-500">Popular:</span>
                        @foreach(['Web Development', 'Mobile App', 'UI/UX Design', 'WordPress', 'React'] as $term)
                            <a href="{{ route('jobs.index', ['search' => $term]) }}"
                                class="px-3 py-1.5 rounded-full bg-white hover:bg-accent-50 border border-accent-200 text-sm text-accent-600 font-medium transition-all shadow-sm hover:shadow-md">
                                {{ $term }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="bg-white dark:bg-surface-800 border-b border-surface-200 dark:border-surface-700 shadow-sm -mt-4 relative z-10 mx-4 sm:mx-6 lg:mx-auto max-w-5xl rounded-2xl">
            <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-surface-200 dark:divide-surface-700">
                <div class="px-6 py-5 text-center">
                    <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($jobs->total()) }}</div>
                    <div class="text-sm text-surface-500 dark:text-surface-400">Open Jobs</div>
                </div>
                <div class="px-6 py-5 text-center">
                    <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ $categories->count() }}</div>
                    <div class="text-sm text-surface-500 dark:text-surface-400">Categories</div>
                </div>
                <div class="px-6 py-5 text-center">
                    <div class="flex items-center justify-center gap-1 text-2xl font-bold text-surface-900 dark:text-white">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        100%
                    </div>
                    <div class="text-sm text-surface-500 dark:text-surface-400">Secure Escrow</div>
                </div>
                <div class="px-6 py-5 text-center">
                    <div class="flex items-center justify-center gap-1 text-2xl font-bold text-accent-600 dark:text-accent-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Fast
                    </div>
                    <div class="text-sm text-surface-500 dark:text-surface-400">Hiring Process</div>
                </div>
            </div>
        </div>

        <!-- Category Pills -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">
                <a href="{{ route('jobs.index', request()->except('category')) }}"
                    class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all {{ !request('category') ? 'bg-accent-600 text-white shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-accent-500 hover:text-accent-600' }}">
                    All Jobs
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('jobs.index', array_merge(request()->except('category'), ['category' => $category->id])) }}"
                        class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('category') == $category->id ? 'bg-accent-600 text-white shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-accent-500 hover:text-accent-600' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-16">
            <!-- Results Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white">
                        @if(request('search'))
                            Results for "{{ request('search') }}"
                        @elseif(request('category'))
                            {{ $categories->firstWhere('id', request('category'))?->name ?? 'Jobs' }}
                        @else
                            Browse Available Jobs
                        @endif
                    </h2>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">{{ number_format($jobs->total()) }} opportunities waiting for you</p>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('jobs.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-accent-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-accent-700 transition-colors shadow-lg shadow-accent-600/25">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Post a Job
                        </a>
                    @endauth

                    <select onchange="window.location.href = this.value" class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-900 dark:text-white focus:border-accent-500 focus:ring-1 focus:ring-accent-500">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'budget_high']) }}" {{ request('sort') == 'budget_high' ? 'selected' : '' }}>Budget: High to Low</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'budget_low']) }}" {{ request('sort') == 'budget_low' ? 'selected' : '' }}>Budget: Low to High</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'proposals_low']) }}" {{ request('sort') == 'proposals_low' ? 'selected' : '' }}>Fewest Proposals</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="w-full lg:w-72 shrink-0">
                    <div class="sticky top-24 space-y-6">
                        <!-- Project Type -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-accent-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Project Type
                            </h3>
                            <div class="space-y-1">
                                <a href="{{ route('jobs.index', request()->except('budget_type')) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-colors {{ !request('budget_type') ? 'bg-accent-50 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    <span>All Types</span>
                                </a>
                                <a href="{{ route('jobs.index', array_merge(request()->all(), ['budget_type' => 'fixed'])) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-colors {{ request('budget_type') == 'fixed' ? 'bg-accent-50 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    <span>Fixed Price</span>
                                </a>
                                <a href="{{ route('jobs.index', array_merge(request()->all(), ['budget_type' => 'hourly'])) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-colors {{ request('budget_type') == 'hourly' ? 'bg-accent-50 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    <span>Hourly Rate</span>
                                </a>
                            </div>
                        </div>

                        <!-- Experience Level -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-accent-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Experience Level
                            </h3>
                            <div class="space-y-1">
                                <a href="{{ route('jobs.index', request()->except('experience')) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-colors {{ !request('experience') ? 'bg-accent-50 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    <span>Any Level</span>
                                </a>
                                @foreach(\App\Models\JobPosting::EXPERIENCE_LEVELS as $value => $label)
                                    <a href="{{ route('jobs.index', array_merge(request()->all(), ['experience' => $value])) }}"
                                        class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-colors {{ request('experience') == $value ? 'bg-accent-50 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                        <span>{{ $label }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Budget Range -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-accent-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Budget Range
                            </h3>
                            <div class="space-y-1">
                                @php
                                    $budgetRanges = [
                                        ['label' => 'Under $100', 'max' => 100],
                                        ['label' => '$100 - $500', 'min' => 100, 'max' => 500],
                                        ['label' => '$500 - $1,000', 'min' => 500, 'max' => 1000],
                                        ['label' => '$1,000 - $5,000', 'min' => 1000, 'max' => 5000],
                                        ['label' => '$5,000+', 'min' => 5000],
                                    ];
                                @endphp
                                @foreach($budgetRanges as $range)
                                    @php
                                        $isActive = (request('min_budget') == ($range['min'] ?? null) && request('max_budget') == ($range['max'] ?? null));
                                        $params = request()->except(['min_budget', 'max_budget']);
                                        if (isset($range['min'])) $params['min_budget'] = $range['min'];
                                        if (isset($range['max'])) $params['max_budget'] = $range['max'];
                                    @endphp
                                    <a href="{{ route('jobs.index', $params) }}"
                                        class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ $isActive ? 'bg-accent-50 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                        {{ $range['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Clear Filters -->
                        @if(request()->hasAny(['search', 'category', 'budget_type', 'experience', 'min_budget', 'max_budget']))
                            <a href="{{ route('jobs.index') }}" class="flex items-center justify-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-3 text-sm font-medium text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear All Filters
                            </a>
                        @endif

                        <!-- How It Works -->
                        <div class="rounded-2xl bg-gradient-to-br from-accent-50 to-purple-50 dark:from-accent-900/20 dark:to-purple-900/20 border border-accent-100 dark:border-accent-800/50 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-accent-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                How It Works
                            </h3>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-accent-200 dark:bg-accent-800 flex items-center justify-center text-xs font-bold text-accent-700 dark:text-accent-300 shrink-0">1</span>
                                    <span class="text-surface-600 dark:text-surface-400">Browse jobs that match your skills</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-accent-200 dark:bg-accent-800 flex items-center justify-center text-xs font-bold text-accent-700 dark:text-accent-300 shrink-0">2</span>
                                    <span class="text-surface-600 dark:text-surface-400">Submit a compelling proposal</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-accent-200 dark:bg-accent-800 flex items-center justify-center text-xs font-bold text-accent-700 dark:text-accent-300 shrink-0">3</span>
                                    <span class="text-surface-600 dark:text-surface-400">Get hired and start working</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-accent-200 dark:bg-accent-800 flex items-center justify-center text-xs font-bold text-accent-700 dark:text-accent-300 shrink-0">4</span>
                                    <span class="text-surface-600 dark:text-surface-400">Get paid via secure escrow</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Escrow Protection -->
                        <div class="rounded-2xl border border-green-200 dark:border-green-800/50 bg-green-50 dark:bg-green-900/20 p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-800/50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-green-900 dark:text-green-100">Payment Protection</h4>
                                    <p class="text-xs text-green-700 dark:text-green-300">Secure milestone escrow</p>
                                </div>
                            </div>
                            <p class="text-sm text-green-700 dark:text-green-300">Funds are held in escrow until milestones are approved. Your payment is guaranteed.</p>
                        </div>
                    </div>
                </aside>

                <!-- Jobs List -->
                <div class="flex-1">
                    @if($jobs->count() > 0)
                        <div class="space-y-4">
                            @foreach($jobs as $job)
                                <x-job-card :job="$job" />
                            @endforeach
                        </div>

                        @if($jobs->hasPages())
                            <div class="mt-8">
                                {{ $jobs->withQueryString()->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700">
                            <div class="w-20 h-20 rounded-2xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-accent-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No Jobs Found</h3>
                            <p class="text-surface-500 dark:text-surface-400 mb-6 max-w-md mx-auto">We couldn't find any jobs matching your criteria. Try adjusting your filters or check back later.</p>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-accent-600 px-6 py-3 text-sm font-semibold text-white hover:bg-accent-700 transition-colors shadow-lg shadow-accent-600/25">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    View All Jobs
                                </a>
                                <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-surface-300 dark:border-surface-600 px-6 py-3 text-sm font-semibold text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    Browse Services
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="border-t border-surface-200 dark:border-surface-700">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
                <div class="relative bg-gradient-to-br from-accent-50 via-purple-50 to-pink-50 dark:from-accent-900/20 dark:via-purple-900/20 dark:to-pink-900/20 rounded-3xl overflow-hidden border border-accent-100 dark:border-accent-800">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M15 0v30M0 15h30\" stroke=\"%23a855f7\" stroke-opacity=\"0.05\" stroke-width=\"0.5\"/%3E%3C/svg%3E')]"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-accent-200/40 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-200/40 rounded-full blur-3xl"></div>

                    <div class="relative px-8 py-12 sm:px-12 lg:px-16 lg:py-16 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                        <div class="max-w-xl">
                            <h2 class="text-2xl sm:text-3xl font-bold text-surface-900 dark:text-white mb-4">Need to Hire a Freelancer?</h2>
                            <p class="text-surface-600 dark:text-surface-400 text-lg">Post your project and receive proposals from skilled freelancers within hours. Our secure escrow system protects your investment.</p>
                            <div class="mt-6 flex flex-wrap gap-6">
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Free to post
                                </div>
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Secure escrow
                                </div>
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Fast hiring
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4">
                            @auth
                                <a href="{{ route('jobs.create') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-accent-600 text-white font-semibold hover:bg-accent-700 transition-colors shadow-lg shadow-accent-500/25">
                                    Post a Job
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-accent-600 text-white font-semibold hover:bg-accent-700 transition-colors shadow-lg shadow-accent-500/25">
                                    Get Started
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            @endauth
                            <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-white border border-surface-200 text-surface-700 font-semibold hover:bg-surface-50 hover:border-surface-300 transition-colors shadow-sm">
                                Browse Services
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
