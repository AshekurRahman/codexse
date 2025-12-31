<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-surface-800 dark:text-surface-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-surface-800 overflow-hidden shadow-sm sm:rounded-lg border border-surface-200 dark:border-surface-700">
                <div class="p-6 text-surface-900 dark:text-surface-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
