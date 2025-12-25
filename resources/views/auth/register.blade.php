<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Create an account</h1>
        <p class="mt-2 text-surface-600 dark:text-surface-400">Join Codexse and start exploring</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create a strong password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms -->
        <div class="mt-6">
            <label class="inline-flex items-start cursor-pointer">
                <input type="checkbox" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-surface-700 mt-0.5" name="terms" required>
                <span class="ms-2 text-sm text-surface-600 dark:text-surface-400">
                    I agree to the <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">Terms of Service</a> and <a href="{{ route('privacy') }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">Privacy Policy</a>
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <x-primary-button class="w-full">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>

        <!-- Login Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-surface-600 dark:text-surface-400">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                    Sign in
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
