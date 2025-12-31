@props([
    'type' => 'default',
    'size' => 'sm',
    'icon' => null
])

@php
    $baseClasses = 'inline-flex items-center font-medium';

    $sizes = [
        'xs' => 'px-1.5 py-0.5 text-xs gap-0.5',
        'sm' => 'px-2.5 py-1 text-xs gap-1',
        'md' => 'px-3 py-1.5 text-sm gap-1.5',
    ];

    $types = [
        'default' => 'bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300',
        'primary' => 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300',
        'success' => 'bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-300',
        'warning' => 'bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-300',
        'danger' => 'bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-300',
        'info' => 'bg-info-100 dark:bg-info-900/30 text-info-700 dark:text-info-300',

        // Solid variants
        'primary-solid' => 'bg-primary-600 text-white',
        'success-solid' => 'bg-success-600 text-white',
        'warning-solid' => 'bg-warning-500 text-white',
        'danger-solid' => 'bg-danger-600 text-white',

        // Special badges
        'featured' => 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm',
        'verified' => 'bg-primary-600 text-white',
        'sale' => 'bg-gradient-to-r from-danger-500 to-rose-500 text-white shadow-sm',
        'new' => 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-sm',
        'pro' => 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-sm',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['sm'];
    $typeClass = $types[$type] ?? $types['default'];
@endphp

<span {{ $attributes->merge(['class' => "$baseClasses $sizeClass $typeClass rounded-full"]) }}>
    @if($icon === 'star')
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
    @elseif($icon === 'check')
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
    @elseif($icon === 'fire')
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
        </svg>
    @elseif($icon === 'bolt')
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
        </svg>
    @endif
    {{ $slot }}
</span>
