<?php

namespace App\Repositories;
use App\Models\Ticket;

class TicketRepository
{
    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function insert(array $ticketData, $id)
    {
        $newTicket = new Ticket();
        $newTicket->name = $ticketData['name'];
        $newTicket->show_time = $ticketData['show_time'];
        $newTicket->class = $ticketData['class'];
        $newTicket->user_id = $id;
        $newTicket->save(); // Save the new ticket to the database

        return $newTicket;
    }

    public function index()
    {
        return $this->ticket->all();
    }

    public function listByID($id)
    {
        return $this->ticket->where('user_id', $id)->get();
    }

    public function findByID($id)
    {
        return $this->ticket->where('id', $id)->first();
    }
}