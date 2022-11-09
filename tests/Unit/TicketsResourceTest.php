<?php

namespace Tests\Unit;

use Faker\Factory as Faker;
use PHPUnit\Framework\TestCase;

use App\Http\Resources\Ticket as TicketResource;
use App\Http\Resources\TicketsCollection as TicketsResource;

class TicketsResourceTest extends TestCase
{
    /**
     * A basic unit test to check if ticket resource to array works.
     *
     * @return void
     */
    public function test_returned_ticket_is_array ()
    {
        $faker = Faker::create();

        $ticket = [
            'id'        => '7a482d20-73fa-4191-a248-4ad0f8407ccb',
            'subject'   => 'Ticket#1234',
            'content'   => $faker->sentence(rand(10,25)),
            'user_name' => $faker->name(),
            'user_email'=> $faker->companyEmail(),
            'status'    => 1
        ];

        $ticketResource = new TicketResource($ticket);

        $this->assertIsArray($ticketResource->toArray($ticket), 'Check if ticket response from resource is array');
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_returned_tickets_collection_is_array()
    {
        $faker = Faker::create();

        $tickets = [
            'id'        => '7a482d20-73fa-4191-a248-4ad0f8407ccb',
            'subject'   => 'Ticket#1234',
            'content'   => $faker->sentence(rand(10,25)),
            'user_name' => $faker->name(),
            'user_email'=> $faker->companyEmail(),
            'status'    => 1
        ];

        $ticketsCollection = new TicketsResource($tickets);

        $this->assertIsArray($ticketsCollection->toArray($tickets), 'Check if tickets collection response from resource is array');
    }
}
