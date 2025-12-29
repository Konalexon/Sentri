<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case Pending = 'pending';
    case Closed = 'closed';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Open => 'Otwarty',
            self::Pending => 'Oczekujący',
            self::Closed => 'Zamknięty',
        };
    }

    /**
     * Get color for UI display.
     */
    public function color(): string
    {
        return match ($this) {
            self::Open => 'green',
            self::Pending => 'yellow',
            self::Closed => 'gray',
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
