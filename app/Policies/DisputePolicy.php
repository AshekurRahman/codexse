<?php

namespace App\Policies;

use App\Models\Dispute;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DisputePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Dispute $dispute): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Initiator can view
        if ($user->id === $dispute->initiated_by) {
            return true;
        }

        // Check if user is involved in the escrow transaction
        $transaction = $dispute->escrowTransaction;
        if ($transaction) {
            if ($user->id === $transaction->payer_id || $user->id === $transaction->payee_id) {
                return true;
            }
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Dispute $dispute): bool
    {
        // Only admins can update disputes (for resolution)
        return $user->is_admin;
    }

    public function delete(User $user, Dispute $dispute): bool
    {
        return $user->is_admin;
    }

    public function resolve(User $user, Dispute $dispute): bool
    {
        // Only admins can resolve disputes
        return $user->is_admin && in_array($dispute->status, ['open', 'under_review', 'awaiting_response']);
    }

    public function respond(User $user, Dispute $dispute): bool
    {
        // Check if user is involved and dispute is awaiting response
        if ($dispute->status !== 'awaiting_response') {
            return false;
        }

        $transaction = $dispute->escrowTransaction;
        if (!$transaction) {
            return false;
        }

        // The other party (not initiator) can respond
        if ($user->id === $dispute->initiated_by) {
            return false;
        }

        if ($user->id === $transaction->payer_id || $user->id === $transaction->payee_id) {
            return true;
        }

        return false;
    }

    public function addEvidence(User $user, Dispute $dispute): bool
    {
        // Can only add evidence to open/under review disputes
        if (!in_array($dispute->status, ['open', 'under_review', 'awaiting_response'])) {
            return false;
        }

        // Initiator can add evidence
        if ($user->id === $dispute->initiated_by) {
            return true;
        }

        // Other party can add evidence
        $transaction = $dispute->escrowTransaction;
        if ($transaction) {
            if ($user->id === $transaction->payer_id || $user->id === $transaction->payee_id) {
                return true;
            }
        }

        return false;
    }
}
