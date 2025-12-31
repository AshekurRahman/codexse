<x-layouts.app title="Referral Program - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white">Referral Program</h1>
                <p class="mt-2 text-surface-600 dark:text-surface-400">Invite friends and earn rewards when they join and make purchases.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Available Balance</p>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($balance, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-success-100 dark:bg-success-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Total Referrals</p>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $totalReferrals }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-info-100 dark:bg-info-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-info-600 dark:text-info-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Successful</p>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $successfulReferrals }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-warning-100 dark:bg-warning-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-warning-600 dark:text-warning-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Total Earned</p>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($totalEarnings, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Referral Link Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Share Link -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Your Referral Link</h2>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">
                            Share this link with friends. When they sign up and make purchases, you both earn rewards!
                        </p>

                        <div class="flex gap-3" x-data="{ copied: false }">
                            <input
                                type="text"
                                readonly
                                value="{{ $referralLink }}"
                                class="flex-1 rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 text-sm font-mono"
                            >
                            <button
                                @click="navigator.clipboard.writeText('{{ $referralLink }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors flex items-center gap-2"
                            >
                                <svg x-show="!copied" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg x-show="copied" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>

                        <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                            <p class="text-sm text-surface-500 dark:text-surface-400 mb-3">Or share your referral code:</p>
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-surface-100 dark:bg-surface-700 rounded-lg">
                                <span class="font-mono font-bold text-lg text-surface-900 dark:text-white">{{ $referralCode }}</span>
                            </div>
                        </div>

                        <!-- Social Share -->
                        <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                            <p class="text-sm text-surface-500 dark:text-surface-400 mb-3">Share on social media:</p>
                            <div class="flex gap-3">
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode($referralLink) }}&text={{ urlencode('Join me on Codexse and get $' . $signupReward . ' bonus!') }}" target="_blank" class="p-2 bg-[#1DA1F2] text-white rounded-lg hover:opacity-90 transition-opacity">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($referralLink) }}" target="_blank" class="p-2 bg-[#1877F2] text-white rounded-lg hover:opacity-90 transition-opacity">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode('Join me on Codexse and get $' . $signupReward . ' bonus! ' . $referralLink) }}" target="_blank" class="p-2 bg-[#25D366] text-white rounded-lg hover:opacity-90 transition-opacity">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                                <a href="mailto:?subject={{ urlencode('Join Codexse') }}&body={{ urlencode('Join me on Codexse and get $' . $signupReward . ' bonus! ' . $referralLink) }}" class="p-2 bg-surface-600 text-white rounded-lg hover:opacity-90 transition-opacity">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Referrals -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Recent Referrals</h2>

                        @if($referredUsers->count() > 0)
                            <div class="space-y-4">
                                @foreach($referredUsers as $referred)
                                    <div class="flex items-center justify-between py-3 border-b border-surface-100 dark:border-surface-700 last:border-0">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-semibold">
                                                {{ strtoupper(substr($referred->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-surface-900 dark:text-white">{{ $referred->name }}</p>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">Joined {{ $referred->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-300 rounded-full">
                                            Active
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="mt-4 text-surface-600 dark:text-surface-400">No referrals yet</p>
                                <p class="text-sm text-surface-500 dark:text-surface-500">Share your link to start earning!</p>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Rewards -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Reward History</h2>

                        @if($recentRewards->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentRewards as $reward)
                                    <div class="flex items-center justify-between py-3 border-b border-surface-100 dark:border-surface-700 last:border-0">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full {{ $reward->type === 'signup' ? 'bg-success-100 dark:bg-success-900/30' : 'bg-primary-100 dark:bg-primary-900/30' }} flex items-center justify-center">
                                                @if($reward->type === 'signup')
                                                    <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-medium text-surface-900 dark:text-white">{{ $reward->description }}</p>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">{{ $reward->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <span class="font-semibold text-success-600 dark:text-success-400">+${{ number_format($reward->amount, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-4 text-surface-600 dark:text-surface-400">No rewards yet</p>
                                <p class="text-sm text-surface-500 dark:text-surface-500">Start referring to earn rewards!</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- How It Works -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">How It Works</h2>

                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-sm">
                                    1
                                </div>
                                <div>
                                    <h4 class="font-medium text-surface-900 dark:text-white">Share Your Link</h4>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Send your unique referral link to friends.</p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-sm">
                                    2
                                </div>
                                <div>
                                    <h4 class="font-medium text-surface-900 dark:text-white">They Sign Up</h4>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">When they register, you both get ${{ number_format($signupReward, 2) }}!</p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-sm">
                                    3
                                </div>
                                <div>
                                    <h4 class="font-medium text-surface-900 dark:text-white">Earn Commission</h4>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Get {{ $purchaseCommission }}% of their purchases!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rewards Info -->
                    <div class="bg-gradient-to-br from-primary-500 to-accent-500 rounded-xl p-6 text-white">
                        <h3 class="text-lg font-semibold mb-4">Rewards Summary</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-primary-100">Signup Bonus</span>
                                <span class="font-bold">${{ number_format($signupReward, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-primary-100">Purchase Commission</span>
                                <span class="font-bold">{{ $purchaseCommission }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-primary-100">Min. Withdrawal</span>
                                <span class="font-bold">${{ number_format($minWithdrawal, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Referred By -->
                    @if($user->referrer)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 mb-3">You were referred by</h3>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-semibold">
                                    {{ strtoupper(substr($user->referrer->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-surface-900 dark:text-white">{{ $user->referrer->name }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
