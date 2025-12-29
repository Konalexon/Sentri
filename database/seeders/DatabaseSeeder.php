<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Message;
use App\Models\Tag;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo users
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@sentri.io',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $agent = User::create([
            'name' => 'Agent Support',
            'email' => 'agent@sentri.io',
            'password' => Hash::make('password'),
            'role' => UserRole::Agent,
            'is_active' => true,
        ]);

        $customer = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'customer@sentri.io',
            'password' => Hash::make('password'),
            'role' => UserRole::Customer,
            'is_active' => true,
        ]);

        // Create tags
        $tags = [
            ['name' => 'Pilne', 'color' => '#ef4444', 'description' => 'Zgłoszenia wymagające natychmiastowej uwagi'],
            ['name' => 'Bug', 'color' => '#f97316', 'description' => 'Błędy w oprogramowaniu'],
            ['name' => 'Feature', 'color' => '#22c55e', 'description' => 'Prośby o nowe funkcjonalności'],
            ['name' => 'Pytanie', 'color' => '#3b82f6', 'description' => 'Pytania dotyczące produktu'],
            ['name' => 'Faktura', 'color' => '#8b5cf6', 'description' => 'Sprawy związane z płatnościami'],
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }

        // Create sample tickets
        $ticket1 = Ticket::create([
            'subject' => 'Problem z logowaniem do aplikacji',
            'description' => 'Nie mogę się zalogować do aplikacji. Po wpisaniu prawidłowych danych wyświetla się błąd "Invalid credentials".',
            'status' => TicketStatus::Open,
            'priority' => TicketPriority::High,
            'customer_id' => $customer->id,
            'agent_id' => $agent->id,
        ]);

        $ticket1->tags()->attach([1, 2]);

        Message::create([
            'ticket_id' => $ticket1->id,
            'content' => 'Nie mogę się zalogować do aplikacji. Po wpisaniu prawidłowych danych wyświetla się błąd "Invalid credentials".',
            'sender_type' => User::class,
            'sender_id' => $customer->id,
        ]);

        Message::create([
            'ticket_id' => $ticket1->id,
            'content' => 'Dzień dobry! Dziękuję za zgłoszenie. Sprawdzę status Pana konta i odezwę się wkrótce.',
            'sender_type' => User::class,
            'sender_id' => $agent->id,
        ]);

        $ticket2 = Ticket::create([
            'subject' => 'Prośba o nową funkcjonalność',
            'description' => 'Czy byłoby możliwe dodanie eksportu raportów do formatu PDF?',
            'status' => TicketStatus::Pending,
            'priority' => TicketPriority::Medium,
            'customer_id' => $customer->id,
            'agent_id' => $agent->id,
        ]);

        $ticket2->tags()->attach([3]);

        Message::create([
            'ticket_id' => $ticket2->id,
            'content' => 'Czy byłoby możliwe dodanie eksportu raportów do formatu PDF?',
            'sender_type' => User::class,
            'sender_id' => $customer->id,
        ]);

        $ticket3 = Ticket::create([
            'subject' => 'Pytanie o cennik',
            'description' => 'Chciałbym dowiedzieć się więcej o planach premium i dostępnych opcjach.',
            'status' => TicketStatus::Closed,
            'priority' => TicketPriority::Low,
            'customer_id' => $customer->id,
            'resolved_at' => now(),
        ]);

        $ticket3->tags()->attach([4]);
    }
}
