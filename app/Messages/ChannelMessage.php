<?php

namespace App\Messages;

class ChannelMessage extends Message
{
    public string $type = 'MESSAGE';
    public string $target = 'CHANNEL';
    public int $channel_id;
    public int $user_id;
    public string $value;

    public function __construct(int $user_id, int $channel_id, string $value)
    {
        $this->user_id = $user_id;
        $this->channel_id = $channel_id;
        $this->value = $value;
    }
}
