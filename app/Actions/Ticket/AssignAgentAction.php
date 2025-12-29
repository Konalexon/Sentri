<?php

namespace App\Actions\Ticket;

use App\Enums\UserRole;
use App\Events\TicketAssigned;
use App\Models\Ticket;
use App\Models\User;
use InvalidArgumentException;

class AssignAgentAction
{
    /**
     * Assign an agent to a ticket.
     *
     * @throws InvalidArgumentException
     */
    public function execute(Ticket $ticket, User $agent): Ticket
    {
        $this->validateAgent($agent);

        $previousAgent = $ticket->agent;

        $ticket->update([
            'agent_id' => $agent->id,
        ]);

        event(new TicketAssigned($ticket, $agent, $previousAgent));

        return $ticket->fresh(['agent']);
    }

    /**
     * Validate that the user can be assigned as an agent.
     *
     * @throws InvalidArgumentException
     */
    private function validateAgent(User $agent): void
    {
        if (!$agent->canManageTickets()) {
            throw new InvalidArgumentException(
                "User {$agent->id} cannot be assigned as an agent. Required role: Agent or Admin."
            );
        }

        if (!$agent->is_active) {
            throw new InvalidArgumentException(
                "Agent {$agent->id} is not active and cannot be assigned to tickets."
            );
        }
    }
}
