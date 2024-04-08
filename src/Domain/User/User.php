<?php

declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    
    
    const USER_ID = APP_ID . 'User_id';
    const USER_CNES = APP_ID . 'User_cnes';
    const USER_NAME = APP_ID . 'User_name';
    const USER_ROLE = APP_ID . 'User_role';
    const USER_LOGIN = APP_ID . 'User_login';
    const USER_SESSION_HASH = APP_ID . 'User_session_hash';
    const USER_SESSION_DOUBLE = APP_ID . 'User_session_double';
    const USER_LANCOU_HOJE = APP_ID . 'User_lancou_hoje';

    public function __construct(public readonly ?int $id, public readonly string $user_name, public readonly int $user_role, public readonly ?string $user_cnes)
    {
        

    }

    

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'user_role' => $this->user_role,
        ];
    }
}
