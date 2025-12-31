<x-layouts.app :title="$jobPosting->title . ' - Jobs - Codexse'">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-surface-500 dark:text-surface-400 mb-6">
                <a href="{{ route('jobs.index') }}" class="hover:text-primary-600">Jobs</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-surface-900 dark:text-white truncate">{{ $jobPosting->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Header -->
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $jobPosting->title }}</h1>
                            <x-status-badge :status="$jobPosting->status" :statuses="\App\Models\JobPosting::STATUSES" />
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-sm text-surface-500 dark:text-surface-400 mb-4">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $jobPosting->client->name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Posted {{ $jobPosting->created_at->diffForHumans() }}
                            </span>
                            @if($jobPosting->category)
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    {{ $jobPosting->category->name }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                                {{ $jobPosting->proposals_count }} proposals
                            </span>
                        </div>

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-4 border-y border-surface-200 dark:border-surface-700">
                            <div>
                                <p class="text-xs text-surface-500 dark:text-surface-400">Budget</p>
                                <p class="font-semibold text-surface-900 dark:text-white">{{ $jobPosting->budget_range }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-surface-500 dark:text-surface-400">Type</p>
                                <p class="font-semibold text-surface-900 dark:text-white">{{ \App\Models\JobPosting::BUDGET_TYPES[$jobPosting->budget_type] ?? $jobPosting->budget_type }}</p>
                            </div>
                            @if($jobPosting->duration_text)
                                <div>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">Duration</p>
                                    <p class="font-semibold text-surface-900 dark:text-white">{{ $jobPosting->duration_text }}</p>
                                </div>
                            @endif
                            @if($jobPosting->experience_level)
                                <div>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">Experience</p>
                                    <p class="font-semibold text-surface-900 dark:text-white">{{ \App\Models\JobPosting::EXPERIENCE_LEVELS[$jobPosting->experience_level] ?? $jobPosting->experience_level }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Project Description</h2>
                        <p class="text-sm text-surface-500 dark:text-surface-400 mb-4">Read the full details of what the client is looking for in this project.</p>
                        <div class="prose prose-surface dark:prose-invert max-w-none">
                            {!! $jobPosting->description !!}
                        </div>
                    </div>

                    <!-- Skills -->
                    @if($jobPosting->skills_required && count($jobPosting->skills_required) > 0)
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Skills & Expertise Required</h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mb-4">The client is looking for freelancers with experience in these skills.</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($jobPosting->skills_required as $skill)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    @if($jobPosting->attachments && count($jobPosting->attachments) > 0)
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Attachments</h2>
                            <div class="space-y-2">
                                @foreach($jobPosting->attachments as $attachment)
                                    <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank"
                                        class="flex items-center gap-3 p-3 rounded-lg border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="text-sm text-surface-700 dark:text-surface-300">{{ $attachment['name'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        <!-- Submit Proposal Card -->
                        @auth
                            @if(auth()->user()->seller && $jobPosting->isOpen())
                                @if($existingProposal)
                                    <div class="rounded-2xl border border-success-200 dark:border-success-800 bg-success-50 dark:bg-success-900/20 p-6">
                                        <div class="text-center">
                                            <div class="w-12 h-12 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center mx-auto mb-3">
                                                <svg class="h-6 w-6 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <h3 class="font-semibold text-surface-900 dark:text-white mb-1">Proposal Submitted Successfully</h3>
                                            <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">You submitted a proposal for {{ $existingProposal->formatted_price }}. The client will review and respond if interested.</p>
                                            <a href="{{ route('seller.proposals.show', $existingProposal) }}"
                                                class="inline-flex items-center justify-center w-full rounded-lg border border-primary-600 px-4 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                                                View Your Proposal
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                                        <h3 class="font-semibold text-surface-900 dark:text-white mb-2">Ready to Apply?</h3>
                                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">Submit a proposal to let the client know why you're the perfect fit for this project. Include your pricing and timeline.</p>
                                        <a href="{{ route('seller.proposals.create', $jobPosting) }}"
                                            class="inline-flex items-center justify-center w-full rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                            Submit Your Proposal
                                        </a>
                                        <p class="text-xs text-center text-surface-500 dark:text-surface-400 mt-3">Payments are protected by our secure escrow system</p>
                                    </div>
                                @endif
                            @elseif($jobPosting->client_id === auth()->id())
                                <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                                    <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Manage Your Job</h3>
                                    <div class="space-y-2">
                                        <a href="{{ route('jobs.proposals', $jobPosting) }}"
                                            class="inline-flex items-center justify-center w-full rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                            View Proposals ({{ $jobPosting->proposals_count }})
                                        </a>
                                        @if(in_array($jobPosting->status, ['draft', 'open']))
                                            <a href="{{ route('jobs.edit', $jobPosting) }}"
                                                class="inline-flex items-center justify-center w-full rounded-lg border border-surface-200 dark:border-surface-700 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                                Edit Job
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                                <h3 class="font-semibold text-surface-900 dark:text-white mb-2">Want to Apply for This Job?</h3>
                                <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">Sign in to your account or create a seller profile to submit proposals and win projects.</p>
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center w-full rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all">
                                    Sign In to Apply
                                </a>
                                <p class="text-xs text-center text-surface-500 dark:text-surface-400 mt-3">New here? <a href="{{ route('register') }}" class="text-primary-600 hover:underline">Create an account</a></p>
                            </div>
                        @endauth

                        <!-- Client Info -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">About the Client</h3>
                            <div class="flex items-center gap-3 mb-4">
                                <img src="{{ $jobPosting->client->avatar_url }}" alt="{{ $jobPosting->client->name }}" class="w-12 h-12 rounded-full object-cover">
                                <div>
                                    <p class="font-medium text-surface-900 dark:text-white">{{ $jobPosting->client->name }}</p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Member since {{ $jobPosting->client->created_at->format('M Y') }}</p>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                                <p class="text-sm text-surface-500 dark:text-surface-400">When you submit a proposal, your bid will be reviewed by the client. If selected, you'll be invited to discuss project details and start a contract.</p>
                            </div>
                        </div>

                        <!-- Payment Protection -->
                        <div class="rounded-2xl bg-gradient-to-br from-success-50 to-success-100 dark:from-success-900/20 dark:to-success-800/20 p-5">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-3">Payment Protection</h3>
                            <ul class="space-y-2 text-sm text-surface-600 dark:text-surface-400">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Secure milestone-based payments</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Funds held in escrow until approved</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Dispute resolution support</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
