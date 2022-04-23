<?php

namespace App\Messages;


class ChannelList extends Message
{
    public string $type = 'RESPONSE';
    public string $target = 'CHANNEL_LIST';
    public array $data;
    protected array $channels = [];

    public function __construct(array $channels)
    {
        array_walk($channels, fn($v) => $this->data[] = $v);
    }
}
