<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Tickets as TicketsModel;

use DB;

class StatsTest extends TestCase
{
    /**
     * Check if stats endpoint loads successfully
     *
     * @return void
     */
    public function test_stats_endpoint_load_successfully()
    {
        $response = $this->get('api/tickets/stats/');

        $response->assertStatus(200);
    }

    /**
     * Check if all the required stats exists in reponse
     *
     * @return void
     */
    public function test_check_all_stat_keys_exist ()
    {
        $response = $this->get('api/tickets/stats/');

        $response->assertJsonStructure([
            'status',
            'stats'  => [
                'total_tickets',
                'processed_tickets',
                'unprocessed_tickets',
                'highest_number_of_tickets',
                'lowest_number_of_tickets',
                'last_order_processed'
            ]
        ]);
    }

    /**
     * Check if returning correct total tickets
     *
     * @return void
     */
    public function test_is_returning_correct_total_tickets ()
    {
        $response = $this->get('api/tickets/stats/');

        $total_tickets = DB::table('tickets')->get()->count();

        $tickets = $response->json();

        $this->assertEquals($tickets['stats']['total_tickets'], $total_tickets);
    }

    /**
     * Check if returning correct processed tickets
     *
     * @return void
     */
    public function test_is_returning_correct_processed_tickets ()
    {
        $response = $this->get('api/tickets/stats/');

        $total_processed_tickets = DB::table('tickets')->where('status', '=', 1)->get()->count();

        $tickets = $response->json();

        $this->assertEquals($tickets['stats']['processed_tickets'], $total_processed_tickets);
    }

    /**
     * Check if returning correct unprocessed tickets
     *
     * @return void
     */
    public function test_is_returning_correct_unprocessed_tickets ()
    {
        $response = $this->get('api/tickets/stats/');

        $total_unprocessed_tickets = DB::table('tickets')->where('status', '=', 0)->get()->count();

        $tickets = $response->json();

        $this->assertEquals($tickets['stats']['unprocessed_tickets'], $total_unprocessed_tickets);
    }

    /**
     * Check if returning correct highest number of tickets by user
     *
     * @return void
     */
    public function test_is_returning_correct_highest_number_of_tickets_by_user ()
    {
        $response = $this->get('api/tickets/stats/');

        $highest_number_of_tickets_by_user =    DB::table('tickets')
            ->selectRaw('user_email, count(user_email) as total')
            ->groupBy('user_email')
            ->orderBy('total', 'desc')
            ->first();

        $tickets = $response->json();

        $this->assertEquals($tickets['stats']['highest_number_of_tickets'], (array) $highest_number_of_tickets_by_user);
    }

    /**
     * Check if returning correct highest number of tickets by user
     *
     * @return void
     */
    public function test_is_returning_correct_lowest_number_of_tickets_by_user ()
    {
        $response = $this->get('api/tickets/stats/');

        $lowest_number_of_tickets_by_user =    DB::table('tickets')
            ->selectRaw('user_email, count(user_email) as total')
            ->groupBy('user_email')
            ->orderBy('total', 'asc')
            ->first();

        $tickets = $response->json();

        $this->assertEquals($tickets['stats']['lowest_number_of_tickets'], (array) $lowest_number_of_tickets_by_user);
    }

    /**
     * Check if returning correct highest number of tickets by user
     *
     * @return void
     */
    public function test_is_returning_correct_last_order_processed ()
    {
        $response = $this->get('api/tickets/stats/');

        $tickets = $response->json();

        $processed_tickets = DB::table('tickets')->where('status','=',1)->count();

        if ($processed_tickets == 0) {
            $this->assertEquals($tickets['stats']['last_order_processed'], false);
        } else {
            $last_processed_order = TicketsModel::areProcessed()->orderBy('updated_at', 'desc')->first()->toArray();
            $this->assertEquals($tickets['stats']['last_order_processed'], $last_processed_order['updated_at']);
        }
    }
}
