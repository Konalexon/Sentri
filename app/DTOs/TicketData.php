<?php

namespace App\DTOs;

use App\Enums\TicketPriority;

readonly class TicketData
{
    public function __construct(
        public string $subject,
        public string $description,
        public TicketPriority $priority = TicketPriority::Medium,
        public array $tagIds = [],
    ) {
    }

    /**
     * Create DTO from validated request data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            subject: $data['subject'],
            description: $data['description'],
            priority: TicketPriority::tryFrom($data['priority'] ?? 'medium') ?? TicketPriority::Medium,
            tagIds: $data['tag_ids'] ?? [],
        );
    }
}
