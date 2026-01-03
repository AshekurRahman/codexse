<x-layouts.app title="Available Jobs">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Available Jobs</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Find jobs that match your skills</p>
                </div>
                <a href="{{ route('seller.proposals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-200 dark:hover:bg-surface-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    My Proposals
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 mb-6">
                <form action="{{ route('seller.jobs.available') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search jobs..."
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white"
                        >
                    </div>
                    <div class="sm:w-48">
                        <select name="category" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white">
                            <option value="">All Categories</option>
                            @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Search
                    </button>
                </form>
            </div>

            <!-- Jobs List -->
            @if($jobs->count() > 0)
                <div class="space-y-4">
                    @foreach($jobs as $job)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <a href="{{ route('seller.jobs.show', $job) }}" class="text-lg font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                                    {{ $job->title }}
                                                </a>
                                                @if($job->is_urgent)
                                                    <span class="px-2 py-0.5 text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300 rounded">Urgent</span>
                                                @endif
                                                @if($job->is_featured)
                                                    <span class="px-2 py-0.5 text-xs font-medium bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-300 rounded">Featured</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                                                Posted by {{ $job->client->name ?? 'Unknown' }} &bull; {{ $job->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    <p class="text-surface-600 dark:text-surface-400 mt-3 line-clamp-2">
                                        {{ Str::limit(strip_tags($job->description), 200) }}
                                    </p>

                                    @if($job->skills && count($job->skills) > 0)
                                        <div class="flex flex-wrap gap-2 mt-4">
                                            @foreach(array_slice($job->skills, 0, 5) as $skill)
                                                <span class="px-2 py-1 bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400 rounded text-xs">{{ $skill }}</span>
                                            @endforeach
                                            @if(count($job->skills) > 5)
                                                <span class="px-2 py-1 text-surface-500 dark:text-surface-400 text-xs">+{{ count($job->skills) - 5 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end gap-3 lg:min-w-[200px]">
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-surface-900 dark:text-white">
                                            @if($job->budget_min && $job->budget_max)
                                                {{ format_price($job->budget_min) }} - {{ format_price($job->budget_max) }}
                                            @elseif($job->budget_min)
                                                From {{ format_price($job->budget_min) }}
                                            @elseif($job->budget_max)
                                                Up to {{ format_price($job->budget_max) }}
                                            @else
                                                Budget not specified
                                            @endif
                                        </p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $job->proposals_count }} proposals</p>
                                    </div>
                                    <a href="{{ route('seller.proposals.create', $job) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                        Apply Now
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $jobs->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-12 text-center">
                    <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No jobs available</h3>
                    <p class="text-surface-600 dark:text-surface-400">Check back later for new opportunities.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
