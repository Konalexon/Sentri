<?php

namespace App\Enums;

enum TicketPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Low => 'Niski',
            self::Medium => 'Średni',
            self::High => 'Wysoki',
            self::Urgent => 'Pilny',
        };
    }

    /**
     * Get color for UI display.
     */
    public function color(): string
    {
        return match ($this) {
            self::Low => 'slate',
            self::Medium => 'blue',
            self::High => 'orange',
            self::Urgent => 'red',
        };
    }

    /**
     * Get all possible values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
