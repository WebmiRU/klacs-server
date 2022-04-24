<?php

namespace App\Messages;


class UserList extends Message
{
    public string $type = 'RESPONSE';
    public string $target = 'USER_LIST';
    public array $data;
    protected array $channels = [];

    public function __construct(array $users)
    {
        array_walk($users, fn($v) => $this->data[] = [
            'id' => $v->id,
            'name' => $v->name,
        ]);
    }
}
