<?php

namespace App\Http\Controllers\Api;

use App\Actions\Message\SendMessageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MessageController extends Controller
{
    public function __construct(
        private readonly SendMessageAction $sendMessageAction
    ) {
    }

    /**
     * Get messages for a ticket.
     *
     * @authenticated
     * @urlParam ticket integer required The ticket ID.
     */
    public function index(Request $request, Ticket $ticket): AnonymousResourceCollection
    {
        $this->authorize('view', $ticket);

        $messages = $ticket->messages()
            ->with(['sender:id,name,email,avatar', 'attachments'])
            ->when(
                !$request->user()->canManageTickets(),
                fn($q) => $q->public() // Hide internal notes from customers
            )
            ->orderBy('created_at', 'asc')
            ->get();

        return MessageResource::collection($messages);
    }

    /**
     * Send a new message to a ticket.
     *
     * @authenticated
     * @urlParam ticket integer required The ticket ID.
     * @bodyParam content string required The message content.
     * @bodyParam is_internal boolean Whether this is an internal note (agents only).
     * @bodyParam attachments file[] Optional file attachments.
     */
    public function store(StoreMessageRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('view', $ticket);

        $message = $this->sendMessageAction->execute(
            $ticket,
            $request->user(),
            $request->validated('content'),
            $request->boolean('is_internal', false),
            $request->file('attachments', [])
        );

        return (new MessageResource($message))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mark a message as read.
     *
     * @authenticated
     * @urlParam message integer required The message ID.
     */
    public function markAsRead(Message $message): JsonResponse
    {
        $this->authorize('view', $message->ticket);

        $message->markAsRead();

        return response()->json([
            'message' => 'Message marked as read.',
        ]);
    }
}
