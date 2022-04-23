<?php

namespace App\Messages;


class AuthError extends Message
{
    public string $type = 'RESPONSE';
    public string $target = 'AUTH';
    public bool $success = false;
    public string $message = 'Auth error, bye!';
}
