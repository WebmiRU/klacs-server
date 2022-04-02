<?php

namespace App\Messages;

class ChannelMessage
{
    public int $user_id;
    public int $channel_id;
    public string $message;

    public function __construct(int $user_id, int $channel_id, string $message)
    {
        $this->user_id = $user_id;
        $this->channel_id = $channel_id;
        $this->message = $message;
    }
}
