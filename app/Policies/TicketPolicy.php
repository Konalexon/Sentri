<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tickets.
     * - Admin: can view all tickets
     * - Agent: can view assigned tickets
     * - Customer: can view own tickets
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can list tickets (filtered by role)
    }

    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Agent can view assigned tickets
        if ($user->isAgent() && $ticket->agent_id === $user->id) {
            return true;
        }

        // Customer can view own tickets
        if ($user->isCustomer() && $ticket->customer_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create tickets
    }

    /**
     * Determine whether the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Admin can update all
        if ($user->isAdmin()) {
            return true;
        }

        // Assigned agent can update
        if ($user->isAgent() && $ticket->agent_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can assign an agent to the ticket.
     */
    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the ticket.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the ticket.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }
}
