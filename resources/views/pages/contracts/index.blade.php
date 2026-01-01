<x-layouts.app title="My Contracts - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Freelance Contracts</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Manage all your project contracts in one place. Track milestone progress, communicate with parties, and ensure secure payments through our escrow system.</p>
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
                        <a href="{{ route('jobs.my-jobs') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            My Job Posts
                        </a>
                        <a href="{{ route('contracts.index') }}" class="flex items-center gap-3 px-4 py-3 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-4 border-primary-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Contracts
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
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Stats -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Total Contracts</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <p class="text-2xl font-bold text-info-600">{{ $stats['active'] ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Active</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <p class="text-2xl font-bold text-success-600">{{ $stats['completed'] ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Completed</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <p class="text-2xl font-bold text-primary-600">{{ format_price($stats['total_value'] ?? 0) }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Total Value</p>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                        <a href="{{ route('contracts.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') && !request('role') ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            All Contracts
                        </a>
                        <a href="{{ route('contracts.index', ['role' => 'client']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('role') === 'client' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            As Client
                        </a>
                        <a href="{{ route('contracts.index', ['role' => 'freelancer']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('role') === 'freelancer' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            As Freelancer
                        </a>
                        <a href="{{ route('contracts.index', ['status' => 'active']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'active' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            Active
                        </a>
                        <a href="{{ route('contracts.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'completed' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            Completed
                        </a>
                    </div>

                    <!-- Contract List -->
                    @if(isset($contracts) && $contracts->count() > 0)
                        <div class="space-y-4">
                            @foreach($contracts as $contract)
                                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                                    <div class="p-6">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <a href="{{ route('contracts.show', $contract) }}" class="text-lg font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 truncate">
                                                        {{ $contract->title ?? $contract->jobPosting->title ?? 'Contract #' . $contract->contract_number }}
                                                    </a>
                                                    <x-status-badge :status="$contract->status" />
                                                </div>
                                                <div class="flex items-center gap-4 mb-3">
                                                    @if($contract->client_id === auth()->id())
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-info-100 dark:bg-info-900/30 text-info-700 dark:text-info-400">
                                                            You are the Client
                                                        </span>
                                                        <span class="text-sm text-surface-600 dark:text-surface-400">
                                                            Freelancer: {{ $contract->seller->user->name ?? 'N/A' }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-accent-100 dark:bg-accent-900/30 text-accent-700 dark:text-accent-400">
                                                            You are the Freelancer
                                                        </span>
                                                        <span class="text-sm text-surface-600 dark:text-surface-400">
                                                            Client: {{ $contract->client->name ?? 'N/A' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-wrap items-center gap-4 text-sm text-surface-500 dark:text-surface-400">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ format_price($contract->total_amount) }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        Started {{ $contract->created_at->format('M d, Y') }}
                                                    </span>
                                                    @if($contract->milestones_count)
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                            </svg>
                                                            {{ $contract->completed_milestones_count ?? 0 }}/{{ $contract->milestones_count }} milestones
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ route('contracts.show', $contract) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                View Details
                                            </a>
                                        </div>

                                        <!-- Milestone Progress -->
                                        @if($contract->milestones_count)
                                            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Milestone Progress</span>
                                                    <span class="text-sm text-surface-500 dark:text-surface-400">{{ round(($contract->completed_milestones_count ?? 0) / $contract->milestones_count * 100) }}%</span>
                                                </div>
                                                <div class="w-full bg-surface-200 dark:bg-surface-700 rounded-full h-2">
                                                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ ($contract->completed_milestones_count ?? 0) / $contract->milestones_count * 100 }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $contracts->links() }}
                        </div>
                    @else
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-6 py-16 text-center">
                            <div class="w-20 h-20 rounded-full bg-surface-100 dark:bg-surface-700 flex items-center justify-center mx-auto mb-6">
                                <svg class="h-10 w-10 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No Contracts Yet</h3>
                            <p class="text-surface-600 dark:text-surface-400 mb-6 max-w-md mx-auto">Contracts are created when a client accepts a freelancer's proposal. Start by posting a job to hire freelancers, or submit proposals to find work.</p>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                                <a href="{{ route('jobs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Post a Job & Hire
                                </a>
                                <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-surface-300 dark:border-surface-600 px-6 py-3 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    Browse Jobs & Apply
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
