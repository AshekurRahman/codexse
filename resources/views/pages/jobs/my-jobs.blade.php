<x-layouts.app title="My Job Posts - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Job Posts</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Manage your job postings and review proposals</p>
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
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Jobs</p>
                        </div>
                        <a href="{{ route('jobs.my-jobs') }}" class="flex items-center gap-3 px-4 py-3 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-4 border-primary-500">
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
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Account</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                    </nav>

                    <!-- Post New Job -->
                    <div class="mt-6">
                        <a href="{{ route('jobs.create') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Post a New Job
                        </a>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Stats -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Total Jobs</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <p class="text-2xl font-bold text-success-600">{{ $stats['open'] ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Open</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <p class="text-2xl font-bold text-info-600">{{ $stats['in_progress'] ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">In Progress</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <p class="text-2xl font-bold text-surface-600">{{ $stats['completed'] ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Completed</p>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                        <a href="{{ route('jobs.my-jobs') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            All Jobs
                        </a>
                        <a href="{{ route('jobs.my-jobs', ['status' => 'open']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'open' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            Open
                        </a>
                        <a href="{{ route('jobs.my-jobs', ['status' => 'in_progress']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'in_progress' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            In Progress
                        </a>
                        <a href="{{ route('jobs.my-jobs', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'completed' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            Completed
                        </a>
                    </div>

                    <!-- Job List -->
                    @if(isset($jobs) && $jobs->count() > 0)
                        <div class="space-y-4">
                            @foreach($jobs as $job)
                                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                                    <div class="p-6">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <a href="{{ route('jobs.show', $job) }}" class="text-lg font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 truncate">
                                                        {{ $job->title }}
                                                    </a>
                                                    <x-status-badge :status="$job->status" />
                                                </div>
                                                <p class="text-sm text-surface-600 dark:text-surface-400 line-clamp-2 mb-4">{{ Str::limit(strip_tags($job->description), 150) }}</p>
                                                <div class="flex flex-wrap items-center gap-4 text-sm text-surface-500 dark:text-surface-400">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $job->budget_range }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        {{ $job->created_at->format('M d, Y') }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                                        </svg>
                                                        {{ $job->proposals_count ?? 0 }} proposals
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('jobs.show', $job) }}" class="p-2 text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors" title="View">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                @if($job->status === 'open')
                                                    <a href="{{ route('jobs.edit', $job) }}" class="p-2 text-surface-400 hover:text-info-600 dark:hover:text-info-400 transition-colors" title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        @if($job->proposals_count > 0 && $job->status === 'open')
                                            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                                                <a href="{{ route('jobs.show', $job) }}#proposals" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                    Review {{ $job->proposals_count }} proposal{{ $job->proposals_count > 1 ? 's' : '' }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $jobs->links() }}
                        </div>
                    @else
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-6 py-16 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No job posts yet</h3>
                            <p class="text-surface-600 dark:text-surface-400 mb-6">Post your first job to find talented freelancers</p>
                            <a href="{{ route('jobs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Post a Job
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
