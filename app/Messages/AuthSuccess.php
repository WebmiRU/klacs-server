<?php

namespace App\Messages;


class AuthSuccess extends Message
{
    public string $type = 'RESPONSE';
    public string $target = 'AUTH';
    public bool $success = true;
    public string $message = 'Auth success, welcome!';
}
