<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;

class Tickets extends Model
{
    use HasFactory, Uuids;

    protected $fillable = ['subject', 'content', 'user_name', 'user_email', 'status'];

    protected $table = 'tickets';

    /**
     * Get ticket status in user-friendly way.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function getStatusAttribute($value)
    {
        return ($value == 0 ? 'unprocessed' : 'processed');
    }

    /**
     * Append ticket subject with random ticket number
     *
     * @param  string  $value
     * @return void
     */
    public function setSubjectAttribute($value)
    {
        $this->attributes['subject'] = $value.'#'.str_pad(rand(10,9999), 4, "0", STR_PAD_LEFT);
    }

    /**
     * Scope a query to only include processed tickets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeAreProcessed($query)
    {

        $query->where('status', 1);
    }

    /**
     * Scope a query to only include processed tickets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeAreUnprocessed($query)
    {
        $query->where('status', 0);
    }

    /**
     * Scope a query to only include tickets from user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeFrom($query, $value)
    {
        $query->where('user_email', $value);
    }

    /**
     * Get total count of tickets based on status
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return int
     */
    public static function getTicketCount($status = 'all') {
        $tickets = new self;
        $total_tickets = $tickets::all()->count();

        switch ($status) {
            case 'processed':
                $total_tickets = $tickets->areProcessed()->count();
            break;

            case 'unprocessed':
                $total_tickets = $tickets->areUnprocessed()->count();
            break;
        }

        return $total_tickets;
    }

    /**
     * Get count of tickets for each user by email
     *
     * @param  void
     * @return int
     */
    public static function getTicketsCountByUser ($order = 'asc') {
        $tickets = new self;

        if ($order != 'asc')
            $order = 'desc';

        $count =    DB::table('tickets')
                        ->selectRaw('user_email, count(user_email) as total')
                        ->groupBy('user_email')
                        ->orderBy('total', $order)
                        ->get();

        return $count;
    }
}
