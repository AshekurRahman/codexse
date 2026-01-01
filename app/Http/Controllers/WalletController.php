<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;

class WalletController extends Controller
{
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
        $request->validate([
            'amount' => 'required|numeric|min:5|max:10000',
        ]);

        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();

        if (!$wallet->is_active || $wallet->is_frozen) {
            return back()->with('error', 'Your wallet is not available for deposits.');
        }

        $amount = (float) $request->amount;

        // Create Stripe checkout session
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
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
            return back()->with('error', 'Unable to process payment. Please try again.');
        }
    }

    /**
     * Handle successful deposit.
     */
    public function depositSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('wallet.index')->with('error', 'Invalid session.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('wallet.index')->with('error', 'Payment was not completed.');
            }

            // Check if already processed
            $existingTransaction = WalletTransaction::where('payment_id', $sessionId)->first();
            if ($existingTransaction) {
                return redirect()->route('wallet.index')->with('info', 'This deposit has already been processed.');
            }

            $userId = $session->metadata->user_id;
            $amount = (float) $session->metadata->amount;

            $wallet = Wallet::where('user_id', $userId)->first();
            if (!$wallet) {
                return redirect()->route('wallet.index')->with('error', 'Wallet not found.');
            }

            // Process the deposit
            $wallet->deposit(
                amount: $amount,
                description: 'Wallet deposit via Stripe',
                paymentMethod: 'stripe',
                paymentId: $sessionId
            );

            return redirect()->route('wallet.index')->with('success', 'Successfully deposited $' . number_format($amount, 2) . ' to your wallet!');
        } catch (\Exception $e) {
            return redirect()->route('wallet.index')->with('error', 'An error occurred while processing your deposit.');
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
