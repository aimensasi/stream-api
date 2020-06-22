<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JoinPostEvent implements ShouldBroadcast{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $signal;
    public $post;
    public $socketId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Post $post, $signal, $socketId){
        $this->post = $post;
        $this->signal = $signal;
        $this->socket_id = $socketId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(){
        return new PrivateChannel('live-stream.' . $this->post->id);
    }
}
