<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\ActivityLogService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;

class WalletController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display wallet dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(10);

        $stats = [
            'total_deposits' => $wallet->transactions()->deposits()->completed()->sum('amount'),
            'total_purchases' => abs($wallet->transactions()->purchases()->completed()->sum('amount')),
            'total_withdrawals' => abs($wallet->transactions()->withdrawals()->completed()->sum('amount')),
            'this_month_deposits' => $wallet->transactions()
                ->deposits()
                ->completed()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return view('pages.wallet.index', compact('wallet', 'transactions', 'stats'));
    }

    /**
     * Show deposit form.
     */
    public function showDeposit()
    {
        $wallet = Auth::user()->getOrCreateWallet();

        $presetAmounts = [10, 25, 50, 100, 250, 500];

        return view('pages.wallet.deposit', compact('wallet', 'presetAmounts'));
    }

    /**
     * Process deposit via Stripe.
     */
    public function deposit(Request $request)
    {
        Log::info('Wallet deposit method called', [
            'amount' => $request->input('amount'),
            'user_id' => Auth::id(),
        ]);

        $request->validate([
            'amount' => 'required|numeric|min:5|max:10000',
        ]);

        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();

        if (!$wallet->is_active || $wallet->is_frozen) {
            return back()->with('error', 'Your wallet is not available for deposits.');
        }

        $amount = (float) $request->amount;

        // Check if Stripe is configured
        if (!$this->stripeService->isConfigured()) {
            Log::warning('Wallet deposit: Stripe not configured', [
                'has_secret' => !empty($this->stripeService->getSecretKey()),
                'has_public' => !empty($this->stripeService->getPublicKey()),
            ]);
            return back()->with('error', 'Payment gateway is not configured.');
        }

        Log::info('Wallet deposit: Creating Stripe session', [
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => $this->stripeService->getCurrency(),
        ]);

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $this->stripeService->getCurrency(),
                        'product_data' => [
                            'name' => 'Wallet Deposit',
                            'description' => 'Add funds to your Codexse wallet',
                        ],
                        'unit_amount' => (int) ($amount * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('wallet.deposit.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('wallet.deposit.cancel'),
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'type' => 'wallet_deposit',
                    'amount' => $amount,
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Wallet deposit error', [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Unable to process payment. Please try again.');
        }
    }

    /**
     * Handle successful deposit.
     * Note: This route works without authentication because SameSite=strict
     * cookies are not sent on cross-site redirects from Stripe.
     */
    public function depositSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('login')->with('error', 'Invalid payment session.');
        }

        try {
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                Log::warning('Wallet deposit: Payment not completed', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status,
                ]);
                return redirect()->route('login')->with('error', 'Payment was not completed.');
            }

            // Check if already processed (idempotency)
            $existingTransaction = WalletTransaction::where('payment_id', $sessionId)->first();
            if ($existingTransaction) {
                // Already processed - redirect to wallet if logged in, otherwise login
                if (auth()->check()) {
                    return redirect()->route('wallet.index')->with('info', 'This deposit has already been processed.');
                }
                return redirect()->route('login')->with('success', 'Your deposit was processed successfully. Please log in to view your wallet.');
            }

            // Get user info from Stripe session metadata
            $userId = (int) $session->metadata->user_id;
            $walletId = (int) $session->metadata->wallet_id;
            $amount = (float) $session->metadata->amount;

            // Validate metadata exists
            if (!$userId || !$amount) {
                Log::error('Wallet deposit: Missing metadata', [
                    'session_id' => $sessionId,
                    'metadata' => $session->metadata,
                ]);
                return redirect()->route('login')->with('error', 'Invalid payment session data.');
            }

            // Find the wallet
            $wallet = Wallet::where('id', $walletId)->where('user_id', $userId)->first();
            if (!$wallet) {
                Log::error('Wallet deposit: Wallet not found', [
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'wallet_id' => $walletId,
                ]);
                return redirect()->route('login')->with('error', 'Wallet not found.');
            }

            // Get the user for logging
            $user = \App\Models\User::find($userId);
            if (!$user) {
                Log::error('Wallet deposit: User not found', [
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                ]);
                return redirect()->route('login')->with('error', 'User not found.');
            }

            // Process the deposit
            $wallet->deposit(
                amount: $amount,
                description: 'Wallet deposit via Stripe',
                paymentMethod: 'stripe',
                paymentId: $sessionId
            );

            // Refresh wallet to get updated balance
            $wallet->refresh();

            // Log the deposit
            ActivityLogService::logWalletDeposit(
                $user,
                $wallet,
                $amount,
                'stripe',
                $sessionId
            );

            Log::info('Wallet deposit successful', [
                'user_id' => $userId,
                'wallet_id' => $walletId,
                'amount' => $amount,
                'session_id' => $sessionId,
            ]);

            // If user is authenticated, redirect to wallet
            if (auth()->check() && auth()->id() === $userId) {
                return redirect()->route('wallet.index')->with('success', 'Successfully deposited $' . number_format($amount, 2) . ' to your wallet!');
            }

            // User not authenticated (session lost due to SameSite=strict), redirect to login with success message
            return redirect()->route('login')->with('success', 'Successfully deposited $' . number_format($amount, 2) . ' to your wallet! Please log in to continue.');

        } catch (\Exception $e) {
            Log::error('Wallet deposit error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('login')->with('error', 'An error occurred while processing your deposit. Please contact support.');
        }
    }

    /**
     * Handle cancelled deposit.
     */
    public function depositCancel()
    {
        return redirect()->route('wallet.deposit')->with('info', 'Deposit was cancelled.');
    }

    /**
     * Show transaction history.
     */
    public function transactions(Request $request)
    {
        $wallet = Auth::user()->getOrCreateWallet();

        $query = $wallet->transactions()->latest();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->paginate(20)->withQueryString();

        $typeOptions = WalletTransaction::getTypeOptions();
        $statusOptions = WalletTransaction::getStatusOptions();

        return view('pages.wallet.transactions', compact('wallet', 'transactions', 'typeOptions', 'statusOptions'));
    }

    /**
     * Show single transaction details.
     */
    public function showTransaction(WalletTransaction $transaction)
    {
        // Ensure user owns this transaction
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        return view('pages.wallet.transaction-detail', compact('transaction'));
    }

    /**
     * Get wallet balance (API endpoint).
     */
    public function balance()
    {
        $wallet = Auth::user()->getOrCreateWallet();

        return response()->json([
            'balance' => $wallet->balance,
            'formatted_balance' => $wallet->formatted_balance,
            'pending_balance' => $wallet->pending_balance,
            'currency' => $wallet->currency,
            'is_active' => $wallet->is_active,
        ]);
    }
}
