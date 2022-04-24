<?php

namespace App\Messages;


abstract class Message
{
    public function __toString(): string
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }
}
