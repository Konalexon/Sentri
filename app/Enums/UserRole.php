<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Agent = 'agent';
    case Customer = 'customer';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Agent => 'Agent',
            self::Customer => 'Klient',
        };
    }

    /**
     * Get all possible values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if role has management permissions.
     */
    public function canManage(): bool
    {
        return in_array($this, [self::Admin, self::Agent]);
    }
}
