<x-layouts.app title="Affiliate Dashboard - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Affiliate Dashboard</h1>
                    <p class="mt-1 text-surface-600 dark:text-surface-400">Track your referrals and earnings</p>
                </div>
                <a href="{{ route('affiliate.settings') }}" class="inline-flex items-center rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </div>

            @if($affiliate->status === 'pending')
                <div class="mb-6 rounded-lg bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 p-4">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-warning-600 dark:text-warning-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-warning-700 dark:text-warning-300">Your affiliate application is pending review. You'll be able to start earning once approved.</p>
                    </div>
                </div>
            @elseif($affiliate->status === 'suspended')
                <div class="mb-6 rounded-lg bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 p-4">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-danger-600 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-danger-700 dark:text-danger-300">Your affiliate account has been suspended. Please contact support for more information.</p>
                    </div>
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($affiliate->total_earnings, 2) }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Total Earnings</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning-100 dark:bg-warning-900/30 text-warning-600 dark:text-warning-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($affiliate->pending_earnings, 2) }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($affiliate->paid_earnings, 2) }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Paid Out</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $affiliate->successful_referrals }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Successful Referrals</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referral Link -->
            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6 mb-8">
                <h2 class="font-semibold text-surface-900 dark:text-white mb-4">Your Referral Link</h2>
                <div class="flex gap-4">
                    <input type="text" value="{{ $affiliate->getReferralUrl() }}" readonly class="flex-1 rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 text-surface-900 dark:text-white">
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $affiliate->getReferralUrl() }}')" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Copy
                    </button>
                </div>
                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">Share this link to earn {{ $affiliate->commission_rate }}% commission on every sale</p>
            </div>

            <!-- Referrals Table -->
            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                    <h2 class="font-semibold text-surface-900 dark:text-white">Recent Referrals</h2>
                </div>
                @if($referrals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-surface-50 dark:bg-surface-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Earnings</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($referrals as $referral)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($referral->referredUser)
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $referral->referredUser->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                                                    <span class="text-surface-900 dark:text-white">{{ $referral->referredUser->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-surface-500 dark:text-surface-400">Pending registration</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                @if($referral->status === 'pending') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                                                @elseif($referral->status === 'registered') bg-info-100 dark:bg-info-900/30 text-info-700 dark:text-info-400
                                                @elseif($referral->status === 'purchased') bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400
                                                @else bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-400
                                                @endif">
                                                {{ ucfirst($referral->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-surface-900 dark:text-white">${{ number_format($referral->commission_amount ?? 0, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-surface-500 dark:text-surface-400">{{ $referral->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                        {{ $referrals->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-surface-600 dark:text-surface-400">No referrals yet. Share your link to start earning!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
