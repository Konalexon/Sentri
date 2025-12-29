<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(6),
            'description' => fake()->paragraphs(3, true),
            'status' => fake()->randomElement(TicketStatus::cases()),
            'priority' => fake()->randomElement(TicketPriority::cases()),
            'customer_id' => User::factory(),
            'agent_id' => null,
            'resolved_at' => null,
        ];
    }

    /**
     * Indicate that the ticket is open.
     */
    public function open(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TicketStatus::Open,
        ]);
    }

    /**
     * Indicate that the ticket is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TicketStatus::Pending,
        ]);
    }

    /**
     * Indicate that the ticket is closed.
     */
    public function closed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TicketStatus::Closed,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Indicate that the ticket is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn(array $attributes) => [
            'priority' => TicketPriority::Urgent,
        ]);
    }

    /**
     * Assign an agent to the ticket.
     */
    public function withAgent(?User $agent = null): static
    {
        return $this->state(fn(array $attributes) => [
            'agent_id' => $agent?->id ?? User::factory()->create(['role' => 'agent'])->id,
        ]);
    }
}
