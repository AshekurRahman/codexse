<?php

namespace App\Http\Controllers;

use App\Models\JobContract;
use App\Models\JobMilestone;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobContractController extends Controller
{
    /**
     * List user's contracts (as client or seller).
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = JobContract::query()
            ->with(['jobPosting', 'seller', 'client', 'milestones']);

        // Get contracts where user is client or seller
        $query->where(function ($q) use ($user) {
            $q->where('client_id', $user->id);
            if ($user->seller) {
                $q->orWhere('seller_id', $user->seller->id);
            }
        });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            if ($request->role === 'client') {
                $query->where('client_id', $user->id);
            } elseif ($request->role === 'seller' && $user->seller) {
                $query->where('seller_id', $user->seller->id);
            }
        }

        $contracts = $query->latest()->paginate(10);

        return view('pages.contracts.index', compact('contracts'));
    }

    /**
     * Show a contract.
     */
    public function show(JobContract $contract)
    {
        $user = auth()->user();

        // Only client or seller can view
        if ($contract->client_id !== $user->id &&
            (!$user->seller || $user->seller->id !== $contract->seller_id)) {
            abort(403);
        }

        $contract->load([
            'jobPosting',
            'proposal',
            'seller.user',
            'client',
            'milestones.escrowTransaction',
            'conversation.messages' => function ($q) {
                $q->with('sender')->latest()->limit(20);
            }
        ]);

        $isClient = $contract->client_id === $user->id;

        return view('pages.contracts.show', compact('contract', 'isClient'));
    }

    /**
     * Fund a milestone (client action).
     */
    public function fundMilestone(JobMilestone $milestone, EscrowService $escrowService)
    {
        $contract = $milestone->contract;

        if ($contract->client_id !== auth()->id()) {
            abort(403);
        }

        if (!$milestone->canFund()) {
            return redirect()->back()->with('error', 'This milestone cannot be funded.');
        }

        // Redirect to escrow checkout
        return redirect()->route('escrow.checkout.milestone', $milestone);
    }

    /**
     * Approve a milestone delivery (client action).
     */
    public function approveMilestone(Request $request, JobMilestone $milestone, EscrowService $escrowService)
    {
        $contract = $milestone->contract;

        if ($contract->client_id !== auth()->id()) {
            abort(403);
        }

        if (!$milestone->canApprove()) {
            return redirect()->back()->with('error', 'This milestone cannot be approved.');
        }

        try {
            DB::beginTransaction();

            $milestone->update([
                'status' => 'completed',
                'approved_at' => now(),
            ]);

            // Release escrow
            if ($milestone->escrowTransaction) {
                $escrowService->releaseFunds($milestone->escrowTransaction, 'Milestone approved by client');
            }

            // Check if all milestones are completed
            $allCompleted = !$contract->milestones()
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->exists();

            if ($allCompleted) {
                $contract->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Update job posting status
                $contract->jobPosting->update(['status' => 'completed']);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Milestone approved! Payment released to freelancer.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve milestone. Please try again.');
        }
    }

    /**
     * Request revision on a milestone delivery (client action).
     */
    public function requestMilestoneRevision(Request $request, JobMilestone $milestone)
    {
        $contract = $milestone->contract;

        if ($contract->client_id !== auth()->id()) {
            abort(403);
        }

        if (!$milestone->canRequestRevision()) {
            return redirect()->back()->with('error', 'Revision cannot be requested for this milestone.');
        }

        $request->validate([
            'revision_notes' => 'required|string|max:2000',
        ]);

        $milestone->update([
            'status' => 'revision_requested',
            'revision_notes' => $request->input('revision_notes'),
        ]);

        return redirect()->back()->with('success', 'Revision requested. The freelancer will be notified.');
    }

    /**
     * Cancel a contract (with mutual agreement or dispute).
     */
    public function cancel(Request $request, JobContract $contract, EscrowService $escrowService)
    {
        $user = auth()->user();

        if ($contract->client_id !== $user->id &&
            (!$user->seller || $user->seller->id !== $contract->seller_id)) {
            abort(403);
        }

        if (!$contract->canCancel()) {
            return redirect()->back()->with('error', 'This contract cannot be cancelled.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Refund any held escrow for unfulfilled milestones
            foreach ($contract->milestones as $milestone) {
                if ($milestone->escrowTransaction && $milestone->escrowTransaction->canRefund()) {
                    $escrowService->refundFunds($milestone->escrowTransaction, 'Contract cancelled');
                }
                if (!in_array($milestone->status, ['completed', 'cancelled'])) {
                    $milestone->update(['status' => 'cancelled']);
                }
            }

            $contract->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->input('reason'),
            ]);

            // Update job posting
            $contract->jobPosting->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('contracts.index')
                ->with('success', 'Contract cancelled. Any held funds have been refunded.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel contract. Please try again.');
        }
    }
}
