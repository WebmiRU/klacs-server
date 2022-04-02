<?php

namespace App\Messages;

class ErrorMessage
{
    public int $code;
    public string $message;

    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }
}
