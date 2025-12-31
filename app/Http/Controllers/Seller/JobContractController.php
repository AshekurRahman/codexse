<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\JobContract;
use App\Models\JobMilestone;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobContractController extends Controller
{
    /**
     * List seller's contracts.
     */
    public function index(Request $request)
    {
        $seller = auth()->user()->seller;

        $query = JobContract::where('seller_id', $seller->id)
            ->with(['jobPosting', 'client', 'milestones']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Active contracts first
        $contracts = $query
            ->orderByRaw("FIELD(status, 'active', 'pending', 'completed', 'cancelled') ASC")
            ->latest()
            ->paginate(10);

        return view('seller.contracts.index', compact('contracts'));
    }

    /**
     * Show contract details.
     */
    public function show(JobContract $contract)
    {
        if ($contract->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }

        $contract->load([
            'jobPosting',
            'proposal',
            'client',
            'milestones.escrowTransaction',
            'conversation.messages' => function ($q) {
                $q->with('sender')->latest()->limit(20);
            }
        ]);

        return view('seller.contracts.show', compact('contract'));
    }

    /**
     * Start working on a milestone.
     */
    public function startMilestone(JobMilestone $milestone)
    {
        $contract = $milestone->contract;

        if ($contract->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }

        if (!$milestone->canStart()) {
            return redirect()->back()->with('error', 'This milestone cannot be started.');
        }

        $milestone->update(['status' => 'in_progress']);

        // Send notification message
        if ($contract->conversation_id) {
            Message::create([
                'conversation_id' => $contract->conversation_id,
                'sender_id' => auth()->id(),
                'body' => "Started working on milestone: {$milestone->title}",
                'message_type' => 'system',
                'metadata' => [
                    'action' => 'milestone_started',
                    'milestone_id' => $milestone->id,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Milestone started!');
    }

    /**
     * Submit milestone for approval.
     */
    public function submitMilestone(Request $request, JobMilestone $milestone)
    {
        $contract = $milestone->contract;

        if ($contract->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }

        if (!$milestone->canSubmit()) {
            return redirect()->back()->with('error', 'This milestone cannot be submitted.');
        }

        $request->validate([
            'notes' => 'required|string|max:5000',
            'files.*' => 'nullable|file|max:51200', // 50MB
        ]);

        // Handle file uploads
        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('milestone-submissions/' . $contract->id, 'public');
                $files[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        $milestone->update([
            'status' => 'submitted',
            'submission_notes' => $request->input('notes'),
            'submission_files' => $files,
            'submitted_at' => now(),
        ]);

        // Send message
        if ($contract->conversation_id) {
            Message::create([
                'conversation_id' => $contract->conversation_id,
                'sender_id' => auth()->id(),
                'body' => "Submitted milestone for review: {$milestone->title}\n\n{$request->input('notes')}",
                'message_type' => 'delivery',
                'metadata' => [
                    'action' => 'milestone_submitted',
                    'milestone_id' => $milestone->id,
                    'files' => $files,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Milestone submitted for approval!');
    }

    /**
     * Request contract extension.
     */
    public function requestExtension(Request $request, JobContract $contract)
    {
        if ($contract->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }

        if (!$contract->isActive()) {
            return redirect()->back()->with('error', 'Extension cannot be requested for this contract.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
            'extension_days' => 'required|integer|min:1|max:30',
        ]);

        // Send extension request message
        if ($contract->conversation_id) {
            Message::create([
                'conversation_id' => $contract->conversation_id,
                'sender_id' => auth()->id(),
                'body' => "Extension request: {$request->input('extension_days')} additional days\n\nReason: {$request->input('reason')}",
                'message_type' => 'system',
                'metadata' => [
                    'action' => 'extension_requested',
                    'extension_days' => $request->input('extension_days'),
                    'reason' => $request->input('reason'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Extension request sent to client.');
    }
}
