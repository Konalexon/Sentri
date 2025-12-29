<?php

namespace App\Http\Controllers\Api;

use App\DTOs\TicketData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Http\Requests\AssignAgentRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketCollection;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService
    ) {
    }

    /**
     * Display a listing of tickets.
     *
     * @authenticated
     * @queryParam status string Filter by status (open, pending, closed).
     * @queryParam priority string Filter by priority (low, medium, high, urgent).
     * @queryParam search string Search in subject and description.
     * @queryParam per_page integer Results per page (default: 15).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Ticket::class);

        $tickets = $this->ticketService->getTicketsForUser(
            $request->user(),
            $request->only(['status', 'priority', 'search', 'agent_id', 'tag_id', 'per_page'])
        );

        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created ticket.
     *
     * @authenticated
     * @bodyParam subject string required The ticket subject. Example: Problem z logowaniem
     * @bodyParam description string required Detailed description. Example: Nie mogę się zalogować do systemu
     * @bodyParam priority string Priority level (low, medium, high, urgent). Example: high
     * @bodyParam tag_ids array Array of tag IDs to attach.
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticketData = TicketData::fromArray($request->validated());

        $ticket = $this->ticketService->createTicket(
            $ticketData,
            $request->user()
        );

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified ticket.
     *
     * @authenticated
     * @urlParam ticket integer required The ticket ID.
     */
    public function show(Ticket $ticket): TicketResource
    {
        $this->authorize('view', $ticket);

        $ticket = $this->ticketService->getTicketWithDetails($ticket);

        return new TicketResource($ticket);
    }

    /**
     * Update ticket status.
     *
     * @authenticated
     * @urlParam ticket integer required The ticket ID.
     * @bodyParam status string required New status (open, pending, closed). Example: pending
     */
    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): TicketResource
    {
        $this->authorize('update', $ticket);

        $ticket = $this->ticketService->updateStatus(
            $ticket,
            $request->getStatus()
        );

        return new TicketResource($ticket);
    }

    /**
     * Assign an agent to the ticket.
     *
     * @authenticated
     * @urlParam ticket integer required The ticket ID.
     * @bodyParam agent_id integer required The agent user ID.
     */
    public function assignAgent(AssignAgentRequest $request, Ticket $ticket): TicketResource
    {
        $this->authorize('assign', $ticket);

        $agent = User::findOrFail($request->validated('agent_id'));

        $ticket = $this->ticketService->assignAgent($ticket, $agent);

        return new TicketResource($ticket);
    }

    /**
     * Get ticket statistics for dashboard.
     *
     * @authenticated
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->ticketService->getStatistics($request->user());

        return response()->json([
            'data' => $stats,
        ]);
    }

    /**
     * Get available agents for assignment.
     *
     * @authenticated
     */
    public function agents(): JsonResponse
    {
        $this->authorize('viewAny', Ticket::class);

        $agents = $this->ticketService->getAvailableAgents();

        return response()->json([
            'data' => $agents->map(fn($agent) => [
                'id' => $agent->id,
                'name' => $agent->name,
                'email' => $agent->email,
                'avatar_url' => $agent->avatar_url,
                'assigned_tickets_count' => $agent->assigned_tickets_count,
            ]),
        ]);
    }
}
