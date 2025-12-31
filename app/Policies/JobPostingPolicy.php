<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobPostingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, JobPosting $job): bool
    {
        // Anyone can view open/public jobs
        if ($job->status === 'open' && $job->visibility === 'public') {
            return true;
        }

        // Owner can view their own jobs
        if ($user->id === $job->client_id) {
            return true;
        }

        // Admins can view all jobs
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, JobPosting $job): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->id === $job->client_id;
    }

    public function delete(User $user, JobPosting $job): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Can only delete if no contracts exist
        if ($job->contracts()->exists()) {
            return false;
        }

        return $user->id === $job->client_id;
    }

    public function restore(User $user, JobPosting $job): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->id === $job->client_id;
    }

    public function forceDelete(User $user, JobPosting $job): bool
    {
        return $user->is_admin;
    }

    public function viewProposals(User $user, JobPosting $job): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->id === $job->client_id;
    }

    public function submitProposal(User $user, JobPosting $job): bool
    {
        // Must be a seller
        if (!$user->seller) {
            return false;
        }

        // Can't submit proposal to own job
        if ($user->id === $job->client_id) {
            return false;
        }

        // Job must be open
        if ($job->status !== 'open') {
            return false;
        }

        // Check if already submitted a proposal
        if ($job->proposals()->where('seller_id', $user->seller->id)->exists()) {
            return false;
        }

        return true;
    }
}
