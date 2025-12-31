@props(['status', 'statuses' => []])

@php
    $label = $statuses[$status] ?? ucfirst(str_replace('_', ' ', $status));

    $colors = match($status) {
        'pending', 'pending_payment', 'pending_requirements', 'pending_funding' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        'ordered', 'open', 'funded' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'in_progress' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
        'delivered', 'submitted' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
        'revision_requested' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
        'completed', 'accepted', 'released' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'cancelled', 'rejected', 'refunded' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        'disputed' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
        'expired', 'paused' => 'bg-surface-100 text-surface-800 dark:bg-surface-800 dark:text-surface-400',
        'held' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
        'draft' => 'bg-surface-100 text-surface-600 dark:bg-surface-800 dark:text-surface-400',
        'shortlisted' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
        'quoted' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400',
        'active', 'published' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
        default => 'bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colors}"]) }}>
    {{ $label }}
</span>
