<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\JobContract;
use App\Models\ServiceOrder;
use App\Rules\SecureFileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisputeController extends Controller
{
    /**
     * Display a listing of the user's disputes.
     */
    public function index()
    {
        $disputes = Dispute::where('initiated_by', auth()->id())
            ->with(['disputable', 'escrowTransaction'])
            ->latest()
            ->paginate(10);

        return view('pages.disputes.index', compact('disputes'));
    }

    /**
     * Show the form for creating a new dispute for a contract.
     */
    public function createForContract(JobContract $contract)
    {
        // Verify user is part of this contract
        if ($contract->client_id !== auth()->id() && $contract->freelancer_id !== auth()->id()) {
            abort(403);
        }

        // Check if contract can be disputed
        if (!in_array($contract->status, ['active', 'in_progress'])) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'This contract cannot be disputed at this stage.');
        }

        // Check if there's already an open dispute
        $existingDispute = Dispute::where('disputable_type', JobContract::class)
            ->where('disputable_id', $contract->id)
            ->whereIn('status', ['open', 'under_review'])
            ->first();

        if ($existingDispute) {
            return redirect()->route('disputes.show', $existingDispute)
                ->with('info', 'There is already an open dispute for this contract.');
        }

        $reasons = Dispute::getReasons();

        return view('pages.disputes.create', [
            'disputableType' => 'contract',
            'disputable' => $contract,
            'reasons' => $reasons,
        ]);
    }

    /**
     * Show the form for creating a new dispute for a service order.
     */
    public function createForServiceOrder(ServiceOrder $serviceOrder)
    {
        // Verify user is part of this order
        if ($serviceOrder->buyer_id !== auth()->id() && $serviceOrder->seller_id !== auth()->user()->seller?->id) {
            abort(403);
        }

        // Check if order can be disputed
        if (!in_array($serviceOrder->status, ['in_progress', 'delivered', 'revision_requested'])) {
            return redirect()->route('service-orders.show', $serviceOrder)
                ->with('error', 'This order cannot be disputed at this stage.');
        }

        // Check if there's already an open dispute
        $existingDispute = Dispute::where('disputable_type', ServiceOrder::class)
            ->where('disputable_id', $serviceOrder->id)
            ->whereIn('status', ['open', 'under_review'])
            ->first();

        if ($existingDispute) {
            return redirect()->route('disputes.show', $existingDispute)
                ->with('info', 'There is already an open dispute for this order.');
        }

        $reasons = Dispute::getReasons();

        return view('pages.disputes.create', [
            'disputableType' => 'service_order',
            'disputable' => $serviceOrder,
            'reasons' => $reasons,
        ]);
    }

    /**
     * Store a newly created dispute.
     */
    public function store(Request $request)
    {
        $request->validate([
            'disputable_type' => 'required|in:contract,service_order',
            'disputable_id' => 'required|integer',
            'reason' => 'required|string|in:' . implode(',', array_keys(Dispute::REASONS)),
            'description' => 'required|string|min:50|max:5000',
            'evidence.*' => ['nullable', 'file', SecureFileUpload::attachment(10)],
        ]);

        // Determine the disputable model
        if ($request->disputable_type === 'contract') {
            $disputable = JobContract::findOrFail($request->disputable_id);
            $disputableClass = JobContract::class;

            // Verify ownership
            if ($disputable->client_id !== auth()->id() && $disputable->freelancer_id !== auth()->id()) {
                abort(403);
            }
        } else {
            $disputable = ServiceOrder::findOrFail($request->disputable_id);
            $disputableClass = ServiceOrder::class;

            // Verify ownership
            if ($disputable->buyer_id !== auth()->id() && $disputable->seller_id !== auth()->user()->seller?->id) {
                abort(403);
            }
        }

        // Check for existing open dispute
        $existingDispute = Dispute::where('disputable_type', $disputableClass)
            ->where('disputable_id', $disputable->id)
            ->whereIn('status', ['open', 'under_review'])
            ->first();

        if ($existingDispute) {
            return redirect()->route('disputes.show', $existingDispute)
                ->with('info', 'There is already an open dispute for this item.');
        }

        try {
            DB::beginTransaction();

            // Handle file uploads
            $evidence = [];
            if ($request->hasFile('evidence')) {
                foreach ($request->file('evidence') as $file) {
                    $path = $file->store('disputes/evidence/' . auth()->id(), 'public');
                    $evidence[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString(),
                    ];
                }
            }

            // Create the dispute
            $dispute = Dispute::create([
                'escrow_transaction_id' => $disputable->escrowTransaction?->id,
                'disputable_type' => $disputableClass,
                'disputable_id' => $disputable->id,
                'initiated_by' => auth()->id(),
                'reason' => $request->reason,
                'description' => $request->description,
                'evidence' => $evidence,
                'status' => 'open',
            ]);

            DB::commit();

            return redirect()->route('disputes.show', $dispute)
                ->with('success', 'Dispute has been submitted. Our team will review it shortly.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit dispute. Please try again.');
        }
    }

    /**
     * Display the specified dispute.
     */
    public function show(Dispute $dispute)
    {
        // Verify user is part of this dispute
        $disputable = $dispute->disputable;

        if ($disputable instanceof JobContract) {
            if ($disputable->client_id !== auth()->id() && $disputable->freelancer_id !== auth()->id()) {
                abort(403);
            }
        } elseif ($disputable instanceof ServiceOrder) {
            if ($disputable->buyer_id !== auth()->id() && $disputable->seller_id !== auth()->user()->seller?->id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $dispute->load(['disputable', 'escrowTransaction', 'initiator', 'resolver']);

        return view('pages.disputes.show', compact('dispute'));
    }

    /**
     * Add evidence to an existing dispute.
     */
    public function addEvidence(Request $request, Dispute $dispute)
    {
        // Verify ownership
        if ($dispute->initiated_by !== auth()->id()) {
            abort(403);
        }

        if (!$dispute->canAddEvidence()) {
            return redirect()->back()->with('error', 'Evidence cannot be added to this dispute.');
        }

        $request->validate([
            'evidence.*' => ['required', 'file', SecureFileUpload::attachment(10)],
            'note' => 'nullable|string|max:1000',
        ]);

        $evidence = $dispute->evidence ?? [];

        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $path = $file->store('disputes/evidence/' . auth()->id(), 'public');
                $evidence[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'note' => $request->note,
                    'uploaded_at' => now()->toISOString(),
                ];
            }
        }

        $dispute->update(['evidence' => $evidence]);

        return redirect()->back()->with('success', 'Evidence added successfully.');
    }

    /**
     * Cancel a dispute (if still open).
     */
    public function cancel(Dispute $dispute)
    {
        if ($dispute->initiated_by !== auth()->id()) {
            abort(403);
        }

        if ($dispute->status !== 'open') {
            return redirect()->back()->with('error', 'Only open disputes can be cancelled.');
        }

        $dispute->update([
            'status' => 'cancelled',
            'resolution_notes' => 'Cancelled by initiator',
        ]);

        return redirect()->route('disputes.index')
            ->with('success', 'Dispute has been cancelled.');
    }
}
