<?php

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/**
 * Register broadcast channel authorization callbacks.
 */

// Private channel for specific ticket
Broadcast::channel('ticket.{ticketId}', function (User $user, int $ticketId) {
    $ticket = Ticket::find($ticketId);

    if (!$ticket) {
        return false;
    }

    // Admin can access all tickets
    if ($user->isAdmin()) {
        return true;
    }

    // Agent can access assigned tickets
    if ($user->isAgent() && $ticket->agent_id === $user->id) {
        return true;
    }

    // Customer can access own tickets
    if ($user->isCustomer() && $ticket->customer_id === $user->id) {
        return true;
    }

    return false;
});

// Private channel for all tickets (agents and admins)
Broadcast::channel('tickets', function (User $user) {
    return $user->canManageTickets();
});

// Private channel for user-specific notifications
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return $user->id === $userId;
});
