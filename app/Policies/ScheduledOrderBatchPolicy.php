<?php

namespace App\Policies;

use App\Models\ScheduledOrderBatch;
use App\Models\User;

class ScheduledOrderBatchPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        // Only admins can manage scheduled batches
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ScheduledOrderBatch $scheduledOrderBatch): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ScheduledOrderBatch $scheduledOrderBatch): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ScheduledOrderBatch $scheduledOrderBatch): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ScheduledOrderBatch $scheduledOrderBatch): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ScheduledOrderBatch $scheduledOrderBatch): bool
    {
        return false;
    }
}
