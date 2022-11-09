<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Tickets;
use App\Http\Resources\Ticket as TicketResource;
use App\Http\Resources\TicketsCollection as TicketsCollection;

use Validator as Val;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('tickets', function () {
    return new TicketsCollection(Tickets::paginate());
});

Route::get('tickets/processed', function () {
    return new TicketsCollection(Tickets::areProcessed()->paginate());
});

Route::get('tickets/unprocessed', function () {
    return new TicketsCollection(Tickets::areUnprocessed()->paginate());
});

Route::get('tickets/from/{email}', function ($email) {

    $validator = Val::make(
            ['email' => $email],
            ['email' => 'email|exists:users']
    );

    if ($validator->fails()) {
        return response()->json(['status' => 'field-errors', 'errors' => $validator->errors()], 400);
    }

    return new TicketsCollection(Tickets::from(strip_tags($email))->paginate());
});

Route::get('tickets/stats', function () {

    $total_tickets              = Tickets::getTicketCount();
    $processed_tickets          = Tickets::getTicketCount('processed');
    $unprocessed_tickets        = Tickets::getTicketCount('unprocessed');
    $highest_number_of_tickets  = Tickets::getTicketsCountByUser('desc')[0];
    $lowest_number_of_tickets   = Tickets::getTicketsCountByUser()[0];
    $last_processed_order       = Tickets::areProcessed()->orderBy('updated_at', 'desc')->first();

    $data = [
        'total_tickets'         => $total_tickets,
        'processed_tickets'     => $processed_tickets,
        'unprocessed_tickets'   => $unprocessed_tickets,
        'highest_number_of_tickets' => $highest_number_of_tickets,
        'lowest_number_of_tickets'  => $lowest_number_of_tickets,
        'last_order_processed'  => ($processed_tickets > 0 ? $last_processed_order->updated_at : false)
    ];

    return response()->json(['status' => 'success', 'stats' => $data], 200);
});

Route::get('tickets/{id}', function ($id) {
    return new TicketResource(Tickets::findOrFail(strip_tags($id)));
});


