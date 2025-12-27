<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoomChangeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $previousHotelName;
    public $privousBuilding;
    public $previousFloor;
    public $previousRoom;
    public $currentHotelName;
    public $currentBuilding;
    public $currentFloor;
    public $currentRoom;

    public function __construct($previousHotelName, $privousBuilding, $previousFloor, $previousRoom, $currentBuilding, $currentFloor, $currentHotelName, $currentRoom)
    {
        $this->previousHotelName = $previousHotelName;
        $this->privousBuilding = $privousBuilding;
        $this->previousFloor = $previousFloor;
        $this->previousRoom = $previousRoom;
        $this->currentBuilding = $currentBuilding;
        $this->currentHotelName = $currentHotelName;
        $this->currentFloor = $currentFloor;
        $this->currentRoom = $currentRoom;

    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Room Change',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.room_change_template',
            with: [
                'previousHotelName' => $this->previousHotelName,
                'privousBuilding' => $this->privousBuilding,
                'previousFloor' => $this->previousFloor,
                'previousRoom' => $this->previousRoom,
                'currentHotelName' => $this->currentHotelName,
                'currentFloor' => $this->currentFloor,
                'currentRoom' => $this->currentRoom,
                'currentBuilding' => $this->currentBuilding,
            ],
        );
    }
}
