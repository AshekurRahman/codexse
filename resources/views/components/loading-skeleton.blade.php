@props(['type' => 'card', 'count' => 1])

@for($i = 0; $i < $count; $i++)
    @if($type === 'card')
        {{-- Product Card Skeleton --}}
        <div class="card overflow-hidden">
            <div class="aspect-[4/3] skeleton-shimmer"></div>
            <div class="p-5 space-y-3">
                <div class="h-3 skeleton-shimmer w-1/4 rounded"></div>
                <div class="h-5 skeleton-shimmer w-3/4 rounded"></div>
                <div class="h-4 skeleton-shimmer w-1/2 rounded"></div>
                <div class="flex justify-between items-center pt-2">
                    <div class="h-6 skeleton-shimmer w-20 rounded"></div>
                    <div class="h-10 skeleton-shimmer w-24 rounded-xl"></div>
                </div>
            </div>
        </div>

    @elseif($type === 'service')
        {{-- Service Card Skeleton --}}
        <div class="card overflow-hidden">
            <div class="aspect-[16/10] skeleton-shimmer"></div>
            <div class="p-5 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full skeleton-shimmer"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 skeleton-shimmer w-24 rounded"></div>
                        <div class="h-3 skeleton-shimmer w-16 rounded"></div>
                    </div>
                </div>
                <div class="h-5 skeleton-shimmer w-full rounded"></div>
                <div class="h-4 skeleton-shimmer w-2/3 rounded"></div>
                <div class="flex justify-between items-center pt-2">
                    <div class="h-3 skeleton-shimmer w-20 rounded"></div>
                    <div class="h-6 skeleton-shimmer w-16 rounded"></div>
                </div>
            </div>
        </div>

    @elseif($type === 'seller')
        {{-- Seller Card Skeleton --}}
        <div class="card p-4 text-center">
            <div class="w-16 h-16 rounded-full skeleton-shimmer mx-auto mb-3"></div>
            <div class="h-4 skeleton-shimmer w-24 mx-auto rounded mb-2"></div>
            <div class="h-3 skeleton-shimmer w-16 mx-auto rounded mb-3"></div>
            <div class="flex justify-center gap-4">
                <div class="h-3 skeleton-shimmer w-12 rounded"></div>
                <div class="h-3 skeleton-shimmer w-12 rounded"></div>
            </div>
        </div>

    @elseif($type === 'testimonial')
        {{-- Testimonial Card Skeleton --}}
        <div class="card p-6">
            <div class="flex gap-1 mb-4">
                @for($j = 0; $j < 5; $j++)
                    <div class="w-5 h-5 skeleton-shimmer rounded"></div>
                @endfor
            </div>
            <div class="space-y-2 mb-6">
                <div class="h-4 skeleton-shimmer w-full rounded"></div>
                <div class="h-4 skeleton-shimmer w-full rounded"></div>
                <div class="h-4 skeleton-shimmer w-3/4 rounded"></div>
            </div>
            <div class="flex items-center gap-4 pt-4 border-t border-surface-100 dark:border-surface-700">
                <div class="w-12 h-12 rounded-full skeleton-shimmer"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 skeleton-shimmer w-24 rounded"></div>
                    <div class="h-3 skeleton-shimmer w-32 rounded"></div>
                </div>
            </div>
        </div>

    @elseif($type === 'table-row')
        {{-- Table Row Skeleton --}}
        <div class="flex items-center gap-4 py-3 px-4 border-b border-surface-100 dark:border-surface-700">
            <div class="w-10 h-10 rounded-lg skeleton-shimmer"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 skeleton-shimmer w-40 rounded"></div>
                <div class="h-3 skeleton-shimmer w-24 rounded"></div>
            </div>
            <div class="h-4 skeleton-shimmer w-20 rounded"></div>
            <div class="h-6 skeleton-shimmer w-16 rounded-full"></div>
        </div>

    @elseif($type === 'stat')
        {{-- Stat Card Skeleton --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl skeleton-shimmer"></div>
                <div class="w-16 h-5 rounded-full skeleton-shimmer"></div>
            </div>
            <div class="h-8 skeleton-shimmer w-24 rounded mb-2"></div>
            <div class="h-4 skeleton-shimmer w-20 rounded"></div>
        </div>

    @elseif($type === 'text-block')
        {{-- Text Block Skeleton --}}
        <div class="space-y-3">
            <div class="h-4 skeleton-shimmer w-full rounded"></div>
            <div class="h-4 skeleton-shimmer w-full rounded"></div>
            <div class="h-4 skeleton-shimmer w-5/6 rounded"></div>
            <div class="h-4 skeleton-shimmer w-4/6 rounded"></div>
        </div>

    @elseif($type === 'avatar')
        {{-- Avatar Skeleton --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full skeleton-shimmer"></div>
            <div class="space-y-2">
                <div class="h-4 skeleton-shimmer w-24 rounded"></div>
                <div class="h-3 skeleton-shimmer w-16 rounded"></div>
            </div>
        </div>

    @elseif($type === 'chart')
        {{-- Chart Skeleton --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="h-5 skeleton-shimmer w-32 rounded"></div>
                <div class="h-8 skeleton-shimmer w-24 rounded-lg"></div>
            </div>
            <div class="h-64 skeleton-shimmer rounded-lg"></div>
        </div>
    @endif
@endfor
