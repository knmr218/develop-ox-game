<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameEnd implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room, $game, $winner;

    /**
     * Create a new event instance.
     */
    public function __construct($room, $game, $winner)
    {
        $this->room = $room;
        $this->game = $game;
        $this->winner = $winner;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return ['room.' . $this->room->id];
    }

    public function broadcastAs()
    {
        return 'GameEnd';
    }

    public function broadcastWith()
    {
        return [
            'board' => $this->game->board,
            'status' => $this->game->status,
            'winner' => $this->winner,
        ];
    }
}
