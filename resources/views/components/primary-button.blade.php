<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-primary-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-surface-800 transition-all duration-150']) }}>
    {{ $slot }}
</button>
