<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Tickets as TicketsModel;
use DB;

class TicketsTest extends TestCase
{
    /**
     * Check loading all tickets
     *
     * @return void
     */
    public function test_tickets_loading()
    {
        $response = $this->get('api/tickets');

        $total_tickets_from_db = DB::table('tickets')->count();
        $total_tickets_from_response = $response->decodeResponseJson()['meta']['total'];

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);

        $this
            ->assertEquals($total_tickets_from_db, $total_tickets_from_response)
        ;
    }

    /**
     * Check loading unprocessed tickets
     *
     * @return void
     */
    public function test_tickets_unprocessed_loading()
    {
        $response = $this->get('api/tickets/unprocessed');

        $total_unprocessed_tickets_from_db = DB::table('tickets')->where('status', '=', 0)->count();
        $total_unprocessed_tickets_from_response = $response->decodeResponseJson()['meta']['total'];

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);

        $this
            ->assertEquals($total_unprocessed_tickets_from_db, $total_unprocessed_tickets_from_response)
        ;
    }

    /**
     * Check loading processed tickets
     *
     * @return void
     */
    public function test_tickets_processed_loading()
    {
        $response = $this->get('api/tickets/processed');

        $total_processed_tickets_from_db = DB::table('tickets')->where('status', '=', 1)->count();
        $total_processed_tickets_from_response = $response->decodeResponseJson()['meta']['total'];

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);

        $this
            ->assertEquals($total_processed_tickets_from_db, $total_processed_tickets_from_response)
        ;
    }

    /**
     * Check loading tickets from wrong email
     *
     * @return void
     */
    public function test_tickets_from_wrong_email_address_not_loading()
    {
        $response = $this->get('api/tickets/from/test@email.com');

        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                'status',
                'errors'
            ]);
        ;
    }

    /**
     * Check loading tickets from correct email
     *
     * @return void
     */
    public function test_tickets_from_correct_email_address_loading ()
    {
        $ticket = DB::table('tickets')->inRandomOrder()->first();

        $response = $this->get('api/tickets/from/'.$ticket->user_email);

        $response->assertStatus(200);
    }
}
