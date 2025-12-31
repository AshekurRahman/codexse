<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobProposal;
use Illuminate\Http\Request;

class JobProposalController extends Controller
{
    /**
     * Browse available jobs.
     */
    public function availableJobs(Request $request)
    {
        $seller = auth()->user()->seller;

        $query = JobPosting::open()
            ->notExpired()
            ->with(['client', 'category'])
            ->withCount('proposals');

        // Exclude jobs already applied to
        $query->whereDoesntHave('proposals', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        });

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $jobs = $query->latest()->paginate(15);

        return view('seller.jobs.available', compact('jobs'));
    }

    /**
     * Show form to submit a proposal.
     */
    public function create(JobPosting $jobPosting)
    {
        if (!$jobPosting->isOpen()) {
            return redirect()->back()->with('error', 'This job is not accepting proposals.');
        }

        $seller = auth()->user()->seller;

        // Check if already submitted
        $existingProposal = $jobPosting->proposals()
            ->where('seller_id', $seller->id)
            ->first();

        if ($existingProposal) {
            return redirect()->route('seller.proposals.show', $existingProposal)
                ->with('info', 'You have already submitted a proposal for this job.');
        }

        return view('seller.jobs.submit-proposal', compact('jobPosting'));
    }

    /**
     * Submit a proposal.
     */
    public function store(Request $request, JobPosting $jobPosting)
    {
        if (!$jobPosting->isOpen()) {
            return redirect()->back()->with('error', 'This job is not accepting proposals.');
        }

        $seller = auth()->user()->seller;

        // Check if already submitted
        if ($jobPosting->proposals()->where('seller_id', $seller->id)->exists()) {
            return redirect()->back()->with('error', 'You have already submitted a proposal for this job.');
        }

        $validated = $request->validate([
            'cover_letter' => 'required|string|max:5000',
            'proposed_price' => 'required|numeric|min:5',
            'proposed_duration' => 'required|integer|min:1',
            'duration_type' => 'required|in:days,weeks,months',
            'milestones' => 'nullable|array',
            'milestones.*.title' => 'required|string|max:200',
            'milestones.*.description' => 'nullable|string|max:500',
            'milestones.*.amount' => 'required|numeric|min:1',
            'milestones.*.due_date' => 'nullable|date|after:today',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        // Validate milestone amounts equal proposed price
        if (!empty($validated['milestones'])) {
            $milestonesTotal = array_sum(array_column($validated['milestones'], 'amount'));
            if (abs($milestonesTotal - $validated['proposed_price']) > 0.01) {
                return redirect()->back()
                    ->with('error', 'Milestone amounts must equal the total proposed price.')
                    ->withInput();
            }
        }

        // Handle attachments
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('proposal-attachments/' . $seller->id, 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        $proposal = JobProposal::create([
            'job_posting_id' => $jobPosting->id,
            'seller_id' => $seller->id,
            'cover_letter' => $validated['cover_letter'],
            'proposed_price' => $validated['proposed_price'],
            'proposed_duration' => $validated['proposed_duration'],
            'duration_type' => $validated['duration_type'],
            'milestones' => $validated['milestones'] ?? null,
            'attachments' => $attachments,
            'status' => 'pending',
        ]);

        // Increment proposals count
        $jobPosting->increment('proposals_count');

        return redirect()->route('seller.proposals.index')
            ->with('success', 'Proposal submitted successfully!');
    }

    /**
     * List seller's proposals.
     */
    public function index(Request $request)
    {
        $seller = auth()->user()->seller;

        $query = JobProposal::where('seller_id', $seller->id)
            ->with(['jobPosting.client']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proposals = $query->latest()->paginate(10);

        return view('seller.jobs.my-proposals', compact('proposals'));
    }

    /**
     * Show a proposal.
     */
    public function show(JobProposal $proposal)
    {
        if ($proposal->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }

        $proposal->load(['jobPosting.client', 'contract']);

        return view('seller.jobs.proposal-show', compact('proposal'));
    }

    /**
     * Edit a pending proposal.
     */
    public function edit(JobProposal $proposal)
    {
        if ($proposal->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }

        if (!$proposal->isPending()) {
            return redirect()->back()->with('error', 'This proposal cannot be edited.');
        }

        return view('seller.jobs.edit-proposal', compact('proposal'));
    }

    /**
     * Update a proposal.
     */
    public function update(Request $request, JobProposal $proposal)
    {
        if ($proposal->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }

        if (!$proposal->isPending()) {
            return redirect()->back()->with('error', 'This proposal cannot be edited.');
        }

        $validated = $request->validate([
            'cover_letter' => 'required|string|max:5000',
            'proposed_price' => 'required|numeric|min:5',
            'proposed_duration' => 'required|integer|min:1',
            'duration_type' => 'required|in:days,weeks,months',
            'milestones' => 'nullable|array',
            'milestones.*.title' => 'required|string|max:200',
            'milestones.*.description' => 'nullable|string|max:500',
            'milestones.*.amount' => 'required|numeric|min:1',
            'milestones.*.due_date' => 'nullable|date|after:today',
        ]);

        // Validate milestone amounts
        if (!empty($validated['milestones'])) {
            $milestonesTotal = array_sum(array_column($validated['milestones'], 'amount'));
            if (abs($milestonesTotal - $validated['proposed_price']) > 0.01) {
                return redirect()->back()
                    ->with('error', 'Milestone amounts must equal the total proposed price.')
                    ->withInput();
            }
        }

        $proposal->update([
            'cover_letter' => $validated['cover_letter'],
            'proposed_price' => $validated['proposed_price'],
            'proposed_duration' => $validated['proposed_duration'],
            'duration_type' => $validated['duration_type'],
            'milestones' => $validated['milestones'] ?? null,
        ]);

        return redirect()->route('seller.proposals.show', $proposal)
            ->with('success', 'Proposal updated successfully!');
    }

    /**
     * Withdraw a proposal.
     */
    public function withdraw(JobProposal $proposal)
    {
        if ($proposal->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }

        if (!$proposal->canWithdraw()) {
            return redirect()->back()->with('error', 'This proposal cannot be withdrawn.');
        }

        $proposal->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now(),
        ]);

        // Decrement proposals count
        $proposal->jobPosting->decrement('proposals_count');

        return redirect()->route('seller.proposals.index')
            ->with('success', 'Proposal withdrawn.');
    }
}
