<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Cron\CronExpression;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

use DB;

class ProcessTicketsTest extends TestCase
{
    /**
     * Check if create ticket jobs are scheduled
     */
    public function test_create_command_scheduled() {
        $schedule = app(Schedule::class);
        $scheduledCommands = collect($schedule->events())
            ->map(function ($event) {
                return $event->command;
            });

        $create_command_scheduled = $scheduledCommands->contains(function ($command) {
            return str_contains($command, 'app:tickets -A create');
        });

        $this->assertEquals($create_command_scheduled, true);
    }

    /**
     * Check if process ticket jobs are scheduled
     */
    public function test_process_command_scheduled() {
        $schedule = app(Schedule::class);
        $scheduledCommands = collect($schedule->events())
            ->map(function ($event) {
                return $event->command;
            });

        $create_command_scheduled = $scheduledCommands->contains(function ($command) {
            return str_contains($command, 'app:tickets -A create');
        });

        $this->assertEquals($create_command_scheduled, true);
    }

    /**
     * A test for checking create console command
     *
     * @return void
     */
    public function test_running_ticket_create_command()
    {
        $total_records_before_create_command = DB::table('tickets')->count();
        $command_response = Artisan::call('app:tickets -A create');
        $total_records_after_create_command = DB::table('tickets')->count();

        $this->assertEquals(($total_records_before_create_command + 1), $total_records_after_create_command);
    }

    /**
     * A test for checking process console command
     *
     * @return void
     */
    public function test_running_ticket_process_command()
    {
        $oldest_unprocessed_order = DB::table('tickets')->where('status', '=', 0)->orderBy('created_at', 'asc')->first();
        $command_response = Artisan::call('app:tickets -A process');
        $latest_processed_order = DB::table('tickets')->where('status', '=', 1)->orderBy('updated_at', 'desc')->first();

        $this->assertEquals($oldest_unprocessed_order->id, $latest_processed_order->id);
    }

    /**
     * A test for checking incorrect console command
     *
     * @return void
     */
    public function test_running_ticket_unknown_command()
    {
        $unprocessed_order_before = DB::table('tickets')->where('status', '=', 0)->count();
        $processed_order_before = DB::table('tickets')->where('status', '=', 1)->count();
        $command_response = Artisan::call('app:tickets -A processessess');

        $unprocessed_order_after = DB::table('tickets')->where('status', '=', 0)->count();
        $processed_order_after = DB::table('tickets')->where('status', '=', 1)->count();

        $this->assertEquals($unprocessed_order_before, $unprocessed_order_after);
        $this->assertEquals($processed_order_before, $processed_order_after);
    }
}
