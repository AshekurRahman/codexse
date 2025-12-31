<?php

namespace App\Policies;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServiceOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ServiceOrder $order): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Buyer can view their orders
        if ($user->id === $order->buyer_id) {
            return true;
        }

        // Seller can view orders for their services
        if ($user->seller && $user->seller->id === $order->seller_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ServiceOrder $order): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Buyer can update (approve, request revision)
        if ($user->id === $order->buyer_id) {
            return true;
        }

        // Seller can update (accept, deliver)
        if ($user->seller && $user->seller->id === $order->seller_id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, ServiceOrder $order): bool
    {
        return $user->is_admin;
    }

    public function approve(User $user, ServiceOrder $order): bool
    {
        // Only buyer can approve
        return $user->id === $order->buyer_id && $order->canApprove();
    }

    public function requestRevision(User $user, ServiceOrder $order): bool
    {
        // Only buyer can request revision
        return $user->id === $order->buyer_id && $order->canRequestRevision();
    }

    public function deliver(User $user, ServiceOrder $order): bool
    {
        // Only seller can deliver
        return $user->seller
            && $user->seller->id === $order->seller_id
            && $order->canDeliver();
    }

    public function cancel(User $user, ServiceOrder $order): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Buyer can cancel pending orders
        if ($user->id === $order->buyer_id && $order->canCancel()) {
            return true;
        }

        return false;
    }

    public function dispute(User $user, ServiceOrder $order): bool
    {
        // Buyer can open dispute
        if ($user->id === $order->buyer_id && $order->canDispute()) {
            return true;
        }

        // Seller can open dispute
        if ($user->seller && $user->seller->id === $order->seller_id && $order->canDispute()) {
            return true;
        }

        return false;
    }
}
