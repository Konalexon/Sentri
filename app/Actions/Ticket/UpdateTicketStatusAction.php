<?php

namespace App\Actions\Ticket;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use InvalidArgumentException;

class UpdateTicketStatusAction
{
    /**
     * Update the status of a ticket with validation.
     *
     * @throws InvalidArgumentException
     */
    public function execute(Ticket $ticket, TicketStatus $newStatus): Ticket
    {
        $this->validateTransition($ticket->status, $newStatus);

        $updates = ['status' => $newStatus];

        // Set resolved_at when closing
        if ($newStatus === TicketStatus::Closed && $ticket->status !== TicketStatus::Closed) {
            $updates['resolved_at'] = now();
        }

        // Clear resolved_at when reopening
        if ($newStatus === TicketStatus::Open && $ticket->status === TicketStatus::Closed) {
            $updates['resolved_at'] = null;
        }

        $ticket->update($updates);

        return $ticket->fresh();
    }

    /**
     * Validate status transition is allowed.
     *
     * @throws InvalidArgumentException
     */
    private function validateTransition(TicketStatus $currentStatus, TicketStatus $newStatus): void
    {
        $allowedTransitions = [
            TicketStatus::Open->value => [
                TicketStatus::Pending->value,
                TicketStatus::Closed->value,
            ],
            TicketStatus::Pending->value => [
                TicketStatus::Open->value,
                TicketStatus::Closed->value,
            ],
            TicketStatus::Closed->value => [
                TicketStatus::Open->value, // Allow reopening
            ],
        ];

        if ($currentStatus === $newStatus) {
            return; // No change, allow
        }

        if (!in_array($newStatus->value, $allowedTransitions[$currentStatus->value] ?? [])) {
            throw new InvalidArgumentException(
                "Cannot transition from {$currentStatus->label()} to {$newStatus->label()}."
            );
        }
    }
}
