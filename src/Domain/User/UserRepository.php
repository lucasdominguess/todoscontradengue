<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\User;

interface UserRepository
{
    // /**
    //  * @return User[]
    //  */

    
    public function validarRecaptcha($token):array;

    public function logar(string $login, string $pass): User;
}
