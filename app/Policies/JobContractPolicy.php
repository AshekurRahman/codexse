<?php

namespace App\Policies;

use App\Models\JobContract;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobContractPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, JobContract $contract): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Client can view
        if ($user->id === $contract->client_id) {
            return true;
        }

        // Seller can view
        if ($user->seller && $user->seller->id === $contract->seller_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, JobContract $contract): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Client can update (approve milestones)
        if ($user->id === $contract->client_id) {
            return true;
        }

        // Seller can update (submit milestones)
        if ($user->seller && $user->seller->id === $contract->seller_id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, JobContract $contract): bool
    {
        return $user->is_admin;
    }

    public function approveMilestone(User $user, JobContract $contract): bool
    {
        // Only client can approve milestones
        return $user->id === $contract->client_id && $contract->status === 'active';
    }

    public function submitMilestone(User $user, JobContract $contract): bool
    {
        // Only seller can submit milestones
        return $user->seller
            && $user->seller->id === $contract->seller_id
            && $contract->status === 'active';
    }

    public function complete(User $user, JobContract $contract): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Client can complete
        return $user->id === $contract->client_id && $contract->status === 'active';
    }

    public function cancel(User $user, JobContract $contract): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Both parties can request cancellation if contract is pending or active
        if (!in_array($contract->status, ['pending', 'active'])) {
            return false;
        }

        if ($user->id === $contract->client_id) {
            return true;
        }

        if ($user->seller && $user->seller->id === $contract->seller_id) {
            return true;
        }

        return false;
    }

    public function dispute(User $user, JobContract $contract): bool
    {
        // Can only dispute active contracts
        if ($contract->status !== 'active') {
            return false;
        }

        // Client can dispute
        if ($user->id === $contract->client_id) {
            return true;
        }

        // Seller can dispute
        if ($user->seller && $user->seller->id === $contract->seller_id) {
            return true;
        }

        return false;
    }
}
