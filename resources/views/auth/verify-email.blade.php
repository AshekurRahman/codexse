<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Verify your email</h1>
        <p class="mt-2 text-surface-600 dark:text-surface-400">
            Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.
        </p>
    </div>

    <!-- Status Message -->
    <div id="status-message" class="mb-6 p-4 rounded-xl hidden">
        <div class="flex items-center gap-3">
            <svg id="status-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p id="status-text" class="text-sm"></p>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-green-700 dark:text-green-300">
                    A new verification link has been sent to your email address.
                </p>
            </div>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <button type="button" id="resend-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
            <svg id="resend-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <svg id="resend-spinner" class="hidden animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span id="resend-text">{{ __('Resend Verification Email') }}</span>
        </button>

        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-surface-200 dark:border-surface-700 text-sm font-semibold text-surface-700 dark:text-surface-300 hover:border-primary-500 transition-all">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resendBtn = document.getElementById('resend-btn');
            const resendIcon = document.getElementById('resend-icon');
            const resendSpinner = document.getElementById('resend-spinner');
            const resendText = document.getElementById('resend-text');
            const statusMessage = document.getElementById('status-message');
            const statusIcon = document.getElementById('status-icon');
            const statusText = document.getElementById('status-text');

            function setLoading(loading) {
                resendBtn.disabled = loading;
                resendIcon.classList.toggle('hidden', loading);
                resendSpinner.classList.toggle('hidden', !loading);
                resendText.textContent = loading ? 'Sending...' : '{{ __("Resend Verification Email") }}';
            }

            function showStatus(message, isSuccess) {
                statusMessage.classList.remove('hidden', 'bg-green-50', 'dark:bg-green-900/20', 'border-green-200', 'dark:border-green-800', 'bg-red-50', 'dark:bg-red-900/20', 'border-red-200', 'dark:border-red-800');
                statusIcon.classList.remove('text-green-500', 'text-red-500');
                statusText.classList.remove('text-green-700', 'dark:text-green-300', 'text-red-700', 'dark:text-red-300');

                if (isSuccess) {
                    statusMessage.classList.add('bg-green-50', 'dark:bg-green-900/20', 'border', 'border-green-200', 'dark:border-green-800');
                    statusIcon.classList.add('text-green-500');
                    statusText.classList.add('text-green-700', 'dark:text-green-300');
                    statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                } else {
                    statusMessage.classList.add('bg-red-50', 'dark:bg-red-900/20', 'border', 'border-red-200', 'dark:border-red-800');
                    statusIcon.classList.add('text-red-500');
                    statusText.classList.add('text-red-700', 'dark:text-red-300');
                    statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                }

                statusText.textContent = message;
            }

            resendBtn.addEventListener('click', async function() {
                setLoading(true);

                try {
                    const response = await fetch('{{ route("verification.send") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }

                    if (data.success) {
                        showStatus(data.message, true);
                    } else {
                        showStatus(data.message || 'An error occurred. Please try again.', false);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showStatus('An error occurred. Please try again.', false);
                } finally {
                    setLoading(false);
                }
            });
        });
    </script>
</x-guest-layout>
