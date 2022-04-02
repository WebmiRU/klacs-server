<?php

namespace App\Messages;

class IncomingMessage
{
    public string $type;
    public string $target;
    public int $channel_id;
    public string $message;

    public function __construct()
    {

    }
}
