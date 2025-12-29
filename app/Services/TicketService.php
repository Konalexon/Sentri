<?php

namespace App\Services;

use App\Actions\Ticket\AssignAgentAction;
use App\Actions\Ticket\UpdateTicketStatusAction;
use App\DTOs\TicketData;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Events\TicketCreated;
use App\Events\TicketStatusChanged;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(
        private readonly AssignAgentAction $assignAgentAction,
        private readonly UpdateTicketStatusAction $updateStatusAction,
    ) {
    }

    /**
     * Get paginated tickets based on user role.
     */
    public function getTicketsForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Ticket::query()
            ->with(['customer:id,name,email,avatar', 'agent:id,name,email,avatar', 'tags', 'latestMessage'])
            ->when($user->isAgent(), fn(Builder $q) => $q->forAgent($user->id))
            ->when($user->isCustomer(), fn(Builder $q) => $q->forCustomer($user->id));

        // Apply filters
        $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new ticket.
     */
    public function createTicket(TicketData $data, User $customer): Ticket
    {
        return DB::transaction(function () use ($data, $customer) {
            $ticket = Ticket::create([
                'subject' => $data->subject,
                'description' => $data->description,
                'priority' => $data->priority,
                'status' => TicketStatus::Open,
                'customer_id' => $customer->id,
            ]);

            // Attach tags if provided
            if (!empty($data->tagIds)) {
                $ticket->tags()->attach($data->tagIds);
            }

            // Create initial message from description
            $ticket->messages()->create([
                'content' => $data->description,
                'sender_type' => User::class,
                'sender_id' => $customer->id,
            ]);

            event(new TicketCreated($ticket));

            return $ticket->load(['customer', 'tags', 'messages']);
        });
    }

    /**
     * Assign an agent to a ticket.
     */
    public function assignAgent(Ticket $ticket, User $agent): Ticket
    {
        return $this->assignAgentAction->execute($ticket, $agent);
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Ticket $ticket, TicketStatus $status): Ticket
    {
        $ticket = $this->updateStatusAction->execute($ticket, $status);

        event(new TicketStatusChanged($ticket, $status));

        return $ticket;
    }

    /**
     * Get ticket with all relationships.
     */
    public function getTicketWithDetails(Ticket $ticket): Ticket
    {
        return $ticket->load([
            'customer:id,name,email,avatar',
            'agent:id,name,email,avatar',
            'tags',
            'messages' => fn($q) => $q->with(['sender:id,name,email,avatar', 'attachments']),
        ]);
    }

    /**
     * Get available agents for assignment.
     */
    public function getAvailableAgents(): \Illuminate\Database\Eloquent\Collection
    {
        return User::agents()
            ->active()
            ->withCount(['assignedTickets' => fn($q) => $q->whereNot('status', TicketStatus::Closed)])
            ->orderBy('assigned_tickets_count')
            ->get();
    }

    /**
     * Get ticket statistics for dashboard.
     */
    public function getStatistics(User $user): array
    {
        $baseQuery = Ticket::query()
            ->when($user->isAgent(), fn(Builder $q) => $q->forAgent($user->id))
            ->when($user->isCustomer(), fn(Builder $q) => $q->forCustomer($user->id));

        return [
            'total' => (clone $baseQuery)->count(),
            'open' => (clone $baseQuery)->open()->count(),
            'pending' => (clone $baseQuery)->pending()->count(),
            'closed' => (clone $baseQuery)->closed()->count(),
            'avg_response_time' => $this->calculateAverageResponseTime($user),
        ];
    }

    /**
     * Apply filters to query.
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        $query
            ->when(isset($filters['status']), fn(Builder $q) => $q->where('status', $filters['status']))
            ->when(isset($filters['priority']), fn(Builder $q) => $q->where('priority', $filters['priority']))
            ->when(isset($filters['agent_id']), fn(Builder $q) => $q->where('agent_id', $filters['agent_id']))
            ->when(isset($filters['search']), fn(Builder $q) => $q->where(function (Builder $sq) use ($filters) {
                $sq->where('subject', 'ilike', "%{$filters['search']}%")
                    ->orWhere('description', 'ilike', "%{$filters['search']}%");
            }))
            ->when(isset($filters['tag_id']), fn(Builder $q) => $q->whereHas('tags', fn($tq) => $tq->where('tags.id', $filters['tag_id'])));
    }

    /**
     * Calculate average response time in hours.
     */
    private function calculateAverageResponseTime(User $user): ?float
    {
        // Implementation for analytics
        return null;
    }
}
