<?php

namespace App\Actions\Message;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SendMessageAction
{
    /**
     * Send a new message to a ticket.
     */
    public function execute(
        Ticket $ticket,
        User $sender,
        string $content,
        bool $isInternal = false,
        array $attachments = []
    ): Message {
        $message = $ticket->messages()->create([
            'content' => $content,
            'sender_type' => User::class,
            'sender_id' => $sender->id,
            'is_internal' => $isInternal,
        ]);

        // Handle attachments
        foreach ($attachments as $file) {
            $this->storeAttachment($message, $file);
        }

        // Load relationships
        $message->load(['sender', 'attachments']);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    /**
     * Store an attachment file.
     */
    private function storeAttachment(Message $message, UploadedFile $file): void
    {
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('attachments/' . $message->ticket_id, $filename, 'public');

        $message->attachments()->create([
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
        ]);
    }
}
