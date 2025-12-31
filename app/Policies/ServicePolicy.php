<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Service $service): bool
    {
        // Anyone can view published services
        if ($service->status === 'published') {
            return true;
        }

        // Owner can view their own services
        if ($user->seller && $user->seller->id === $service->seller_id) {
            return true;
        }

        // Admins can view all services
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->seller !== null;
    }

    public function update(User $user, Service $service): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->seller && $user->seller->id === $service->seller_id;
    }

    public function delete(User $user, Service $service): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->seller && $user->seller->id === $service->seller_id;
    }

    public function restore(User $user, Service $service): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->seller && $user->seller->id === $service->seller_id;
    }

    public function forceDelete(User $user, Service $service): bool
    {
        return $user->is_admin;
    }

    public function order(User $user, Service $service): bool
    {
        // Users can order published services that don't belong to them
        if ($service->status !== 'published') {
            return false;
        }

        // Can't order your own service
        if ($user->seller && $user->seller->id === $service->seller_id) {
            return false;
        }

        return true;
    }
}
