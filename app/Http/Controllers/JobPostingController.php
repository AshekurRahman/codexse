<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\JobContract;
use App\Models\JobPosting;
use App\Models\JobProposal;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobPostingController extends Controller
{
    /**
     * Browse all open job postings.
     */
    public function index(Request $request)
    {
        $query = JobPosting::open()
            ->notExpired()
            ->with(['client', 'category']);

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

        // Budget type
        if ($request->filled('budget_type')) {
            $query->where('budget_type', $request->budget_type);
        }

        // Budget range
        if ($request->filled('min_budget')) {
            $query->where('budget_max', '>=', $request->min_budget);
        }
        if ($request->filled('max_budget')) {
            $query->where('budget_min', '<=', $request->max_budget);
        }

        // Experience level
        if ($request->filled('experience')) {
            $query->where('experience_level', $request->experience);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'budget_high':
                $query->orderByDesc('budget_max');
                break;
            case 'budget_low':
                $query->orderBy('budget_min');
                break;
            case 'proposals_low':
                $query->orderBy('proposals_count');
                break;
            default:
                $query->latest();
        }

        $jobs = $query->paginate(15)->withQueryString();
        $categories = Category::whereHas('jobPostings')->get();

        return view('pages.jobs.index', compact('jobs', 'categories'));
    }

    /**
     * Show a job posting.
     */
    public function show(JobPosting $jobPosting)
    {
        if ($jobPosting->status === 'draft' && $jobPosting->client_id !== auth()->id()) {
            abort(404);
        }

        $jobPosting->load(['client', 'category']);

        // Check if current user (seller) has already submitted a proposal
        $existingProposal = null;
        if (auth()->check() && auth()->user()->seller) {
            $existingProposal = $jobPosting->proposals()
                ->where('seller_id', auth()->user()->seller->id)
                ->first();
        }

        return view('pages.jobs.show', compact('jobPosting', 'existingProposal'));
    }

    /**
     * Show form to create a job posting.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('pages.jobs.create', compact('categories'));
    }

    /**
     * Store a new job posting.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'requirements' => 'nullable|string|max:5000',
            'category_id' => 'required|exists:categories,id',
            'budget_type' => 'required|in:fixed,hourly',
            'budget_min' => 'required|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'deadline' => 'nullable|date|after:today',
            'duration_type' => 'nullable|in:one_time,ongoing',
            'skills_required' => 'nullable|array',
            'experience_level' => 'nullable|in:entry,intermediate,expert',
            'attachments.*' => 'nullable|file|max:10240',
            'closes_in_days' => 'nullable|integer|min:1|max:90',
            'status' => 'required|in:draft,open',
        ]);

        try {
            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('job-attachments/' . auth()->id(), 'public');
                    $attachments[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }

            $jobPosting = JobPosting::create([
                'client_id' => auth()->id(),
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'requirements' => $validated['requirements'] ?? null,
                'budget_type' => $validated['budget_type'],
                'budget_min' => $validated['budget_min'],
                'budget_max' => $validated['budget_max'],
                'deadline' => $validated['deadline'] ?? null,
                'duration_type' => $validated['duration_type'] ?? null,
                'skills_required' => $validated['skills_required'] ?? [],
                'experience_level' => $validated['experience_level'],
                'attachments' => $attachments,
                'status' => $validated['status'],
                'visibility' => 'public',
                'published_at' => $validated['status'] === 'open' ? now() : null,
                'closes_at' => isset($validated['closes_in_days'])
                    ? now()->addDays($validated['closes_in_days'])
                    : null,
            ]);

            $message = $validated['status'] === 'open'
                ? 'Job posted successfully!'
                : 'Job saved as draft.';

            return redirect()->route('jobs.my-jobs')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create job posting. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show form to edit a job posting.
     */
    public function edit(JobPosting $jobPosting)
    {
        if ($jobPosting->client_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($jobPosting->status, ['draft', 'open'])) {
            return redirect()->back()->with('error', 'This job cannot be edited.');
        }

        $categories = Category::orderBy('name')->get();
        return view('pages.jobs.edit', compact('jobPosting', 'categories'));
    }

    /**
     * Update a job posting.
     */
    public function update(Request $request, JobPosting $jobPosting)
    {
        if ($jobPosting->client_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($jobPosting->status, ['draft', 'open'])) {
            return redirect()->back()->with('error', 'This job cannot be edited.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'requirements' => 'nullable|string|max:5000',
            'category_id' => 'required|exists:categories,id',
            'budget_type' => 'required|in:fixed,hourly',
            'budget_min' => 'required|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'deadline' => 'nullable|date|after:today',
            'duration_type' => 'nullable|in:one_time,ongoing',
            'skills_required' => 'nullable|array',
            'experience_level' => 'nullable|in:entry,intermediate,expert',
            'attachments.*' => 'nullable|file|max:10240',
            'closes_in_days' => 'nullable|integer|min:1|max:90',
            'status' => 'required|in:draft,open',
        ]);

        // Handle new file uploads
        $attachments = $jobPosting->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('job-attachments/' . auth()->id(), 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        $jobPosting->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'requirements' => $validated['requirements'] ?? null,
            'budget_type' => $validated['budget_type'],
            'budget_min' => $validated['budget_min'],
            'budget_max' => $validated['budget_max'],
            'deadline' => $validated['deadline'] ?? null,
            'duration_type' => $validated['duration_type'] ?? null,
            'skills_required' => $validated['skills_required'] ?? [],
            'experience_level' => $validated['experience_level'],
            'attachments' => $attachments,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'open' && !$jobPosting->published_at ? now() : $jobPosting->published_at,
            'closes_at' => isset($validated['closes_in_days'])
                ? now()->addDays($validated['closes_in_days'])
                : $jobPosting->closes_at,
        ]);

        return redirect()->route('jobs.my-jobs')
            ->with('success', 'Job updated successfully!');
    }

    /**
     * List client's own job postings.
     */
    public function myJobs(Request $request)
    {
        $query = JobPosting::where('client_id', auth()->id())
            ->with(['category', 'proposals']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(10);

        return view('pages.jobs.my-jobs', compact('jobs'));
    }

    /**
     * View proposals for a job.
     */
    public function proposals(JobPosting $jobPosting)
    {
        if ($jobPosting->client_id !== auth()->id()) {
            abort(403);
        }

        $jobPosting->load(['proposals.seller.user']);

        return view('pages.jobs.proposals', compact('jobPosting'));
    }

    /**
     * Accept a proposal and create a contract.
     */
    public function acceptProposal(Request $request, JobPosting $jobPosting, JobProposal $proposal, EscrowService $escrowService)
    {
        if ($jobPosting->client_id !== auth()->id()) {
            abort(403);
        }

        if (!$proposal->canAccept()) {
            return redirect()->back()->with('error', 'This proposal cannot be accepted.');
        }

        try {
            DB::beginTransaction();

            // Update proposal status
            $proposal->update(['status' => 'accepted']);

            // Reject all other proposals
            $jobPosting->proposals()
                ->where('id', '!=', $proposal->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                    'rejection_reason' => 'Another proposal was accepted',
                ]);

            // Calculate fees (using job commission rate)
            $fees = $escrowService->calculateFees($proposal->proposed_price, 'job');

            // Create conversation
            $conversation = Conversation::create([
                'buyer_id' => auth()->id(),
                'seller_id' => $proposal->seller_id,
                'type' => 'job_contract',
                'subject' => 'Contract: ' . $jobPosting->title,
            ]);

            // Create contract
            $contract = JobContract::create([
                'job_posting_id' => $jobPosting->id,
                'proposal_id' => $proposal->id,
                'client_id' => auth()->id(),
                'seller_id' => $proposal->seller_id,
                'conversation_id' => $conversation->id,
                'title' => $jobPosting->title,
                'description' => $proposal->cover_letter,
                'total_amount' => $proposal->proposed_price,
                'platform_fee' => $fees['platform_fee'],
                'seller_amount' => $fees['seller_amount'],
                'payment_type' => $proposal->milestones && count($proposal->milestones) > 0
                    ? 'milestone'
                    : 'fixed',
                'status' => 'pending',
            ]);

            // Create milestones if defined in proposal
            if ($proposal->milestones && count($proposal->milestones) > 0) {
                foreach ($proposal->milestones as $index => $milestone) {
                    $contract->milestones()->create([
                        'title' => $milestone['title'] ?? 'Milestone ' . ($index + 1),
                        'description' => $milestone['description'] ?? null,
                        'amount' => $milestone['amount'],
                        'due_date' => $milestone['due_date'] ?? null,
                        'sort_order' => $index,
                        'status' => 'pending',
                    ]);
                }
            } else {
                // Create single milestone for fixed price
                $contract->milestones()->create([
                    'title' => 'Project Completion',
                    'amount' => $proposal->proposed_price,
                    'sort_order' => 0,
                    'status' => 'pending',
                ]);
            }

            // Update job status
            $jobPosting->update(['status' => 'in_progress']);

            // Update conversation with contract reference
            $conversation->update([
                'conversationable_type' => JobContract::class,
                'conversationable_id' => $contract->id,
            ]);

            DB::commit();

            return redirect()->route('contracts.show', $contract)
                ->with('success', 'Proposal accepted! Contract created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to accept proposal. Please try again.');
        }
    }

    /**
     * Reject a proposal.
     */
    public function rejectProposal(Request $request, JobPosting $jobPosting, JobProposal $proposal)
    {
        if ($jobPosting->client_id !== auth()->id()) {
            abort(403);
        }

        if ($proposal->status !== 'pending') {
            return redirect()->back()->with('error', 'This proposal cannot be rejected.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $proposal->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->input('reason'),
        ]);

        return redirect()->back()->with('success', 'Proposal rejected.');
    }

    /**
     * Close a job posting.
     */
    public function close(JobPosting $jobPosting)
    {
        if ($jobPosting->client_id !== auth()->id()) {
            abort(403);
        }

        if ($jobPosting->status !== 'open') {
            return redirect()->back()->with('error', 'This job cannot be closed.');
        }

        $jobPosting->update(['status' => 'cancelled']);

        return redirect()->route('jobs.my-jobs')
            ->with('success', 'Job posting closed.');
    }
}
