@props(['job', 'showActions' => true])

<div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5 hover:shadow-xl hover:shadow-primary-500/5 dark:hover:shadow-primary-500/10 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300">
    <div class="flex flex-col lg:flex-row lg:items-start gap-5">
        <!-- Job Info -->
        <div class="flex-1 min-w-0">
            <!-- Title & Status -->
            <div class="flex items-start gap-3 mb-3">
                <h3 class="flex-1">
                    <a href="{{ route('jobs.show', $job) }}" class="text-lg font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        {{ $job->title }}
                    </a>
                </h3>
                @if($job->is_featured)
                    <x-badge type="featured" icon="star">Featured</x-badge>
                @endif
            </div>

            <!-- Client & Posted -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-surface-500 dark:text-surface-400 mb-3">
                <span class="inline-flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ $job->client->name }}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Posted {{ $job->created_at->diffForHumans() }}
                </span>
                @if($job->category)
                    <span class="inline-flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ $job->category->name }}
                    </span>
                @endif
            </div>

            <!-- Description -->
            <p class="text-surface-600 dark:text-surface-400 text-sm line-clamp-2 mb-4">
                {{ Str::limit(strip_tags($job->description), 200) }}
            </p>

            <!-- Skills -->
            @if($job->skills_required && count($job->skills_required) > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach(array_slice($job->skills_required, 0, 5) as $skill)
                        <x-badge type="default" size="sm">{{ $skill }}</x-badge>
                    @endforeach
                    @if(count($job->skills_required) > 5)
                        <x-badge type="default" size="sm">+{{ count($job->skills_required) - 5 }} more</x-badge>
                    @endif
                </div>
            @endif

            <!-- Meta Info -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-4 border-t border-surface-100 dark:border-surface-700">
                <span class="text-base font-bold text-surface-900 dark:text-white">
                    {{ $job->budget_range }}
                </span>
                @if($job->experience_level)
                    <x-badge type="primary" size="sm">
                        {{ \App\Models\JobPosting::EXPERIENCE_LEVELS[$job->experience_level] ?? $job->experience_level }}
                    </x-badge>
                @endif
                @if($job->duration_text)
                    <span class="text-sm text-surface-500 dark:text-surface-400">
                        Est. {{ $job->duration_text }}
                    </span>
                @endif
                <span class="text-sm text-surface-500 dark:text-surface-400">
                    {{ $job->proposals_count }} proposals
                </span>
            </div>
        </div>

        <!-- Actions -->
        @if($showActions)
            <div class="flex lg:flex-col items-center gap-3 pt-4 lg:pt-0 border-t lg:border-t-0 border-surface-100 dark:border-surface-700">
                <a href="{{ route('jobs.show', $job) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-500/25 transition-all whitespace-nowrap">
                    View Job
                </a>
            </div>
        @endif
    </div>
</div>
