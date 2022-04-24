<?php

namespace App\Messages;


class ChannelMessageList extends Message
{
    public string $type = 'RESPONSE';
    public string $target = 'CHANNEL_MESSAGE';
    public int $channel_id;
    public array $data = [];

    public function __construct(int $channelId, array $messages)
    {
        $this->channel_id = $channelId;

        if ($messages) {
            array_walk($messages, fn($v) => $this->data[] = $v);
        }
    }
}
