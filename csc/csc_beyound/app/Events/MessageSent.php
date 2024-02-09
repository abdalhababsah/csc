<?php
namespace App\Events;

use App\Models\Chat;
use App\Models\Subject;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Subject $subject;
    public string  $message;
    public string $senderName;
    /**
     * Create a new event instance.
     *
     * @param  Chat  $message
     * @return void
     */
    public function __construct(string $message, Subject $subject ,string $senderName)
    {
        $this->message = $message;
        $this->subject = $subject;
        $this->senderName = $senderName;

    }
    /*
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('public-chat-channel');
    }
    public function broadcastAs()
    {
        return 'sendMessage';
    }
}

