<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-surface-800 border-2 border-surface-200 dark:border-surface-700 rounded-xl font-semibold text-sm text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 hover:border-surface-300 dark:hover:border-surface-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-surface-800 disabled:opacity-50 transition-all duration-150']) }}>
    {{ $slot }}
</button>
