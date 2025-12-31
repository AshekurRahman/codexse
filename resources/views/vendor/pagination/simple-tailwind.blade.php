@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-surface-500 bg-white border border-surface-300 cursor-default leading-5 rounded-md dark:text-surface-600 dark:bg-surface-800 dark:border-surface-600">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-surface-700 bg-white border border-surface-300 leading-5 rounded-md hover:text-surface-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-surface-100 active:text-surface-700 transition ease-in-out duration-150 dark:bg-surface-800 dark:border-surface-600 dark:text-surface-300 dark:focus:border-blue-700 dark:active:bg-surface-700 dark:active:text-surface-300">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-surface-700 bg-white border border-surface-300 leading-5 rounded-md hover:text-surface-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-surface-100 active:text-surface-700 transition ease-in-out duration-150 dark:bg-surface-800 dark:border-surface-600 dark:text-surface-300 dark:focus:border-blue-700 dark:active:bg-surface-700 dark:active:text-surface-300">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-surface-500 bg-white border border-surface-300 cursor-default leading-5 rounded-md dark:text-surface-600 dark:bg-surface-800 dark:border-surface-600">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
