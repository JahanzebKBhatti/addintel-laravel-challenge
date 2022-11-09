<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Tickets as Tickets;
use App\Models\User as User;

use Faker\Factory as Faker;

class ProcessTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tickets {--A|action=create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Tickets with option to either open a new ticket (--action=create) or process tickets (--action=process)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->option('action');
        $tickets = new Tickets();

        $faker = Faker::create();

        switch ($action) {
            case 'create':
                $user = User::inRandomOrder()->first();
                $ticket = Tickets::create([
                    'subject'       => 'Ticket',
                    'content'       => $faker->sentence(rand(10,25)),
                    'user_name'     => $user->name,
                    'user_email'    => $user->email,
                    'status'        => 0
                ]);
                $this->info('New ticket created: '.$ticket->id);
            break;

            case 'process':
                $ticket = Tickets::where('status', '=', 0)->oldest()->first()->update(['status' => 1]);
                $this->info('Your chosen ticket: '.$ticket);
            break;

            default:
                $this->error('You have chose non-actionable option: '.$action);
        }
    }
}
