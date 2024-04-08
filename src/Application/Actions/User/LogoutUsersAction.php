<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\Enums\App;
use App\Domain\User\User;
use Psr\Http\Message\ResponseInterface as Response;

class LogoutUsersAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
       
        $response = ['cod' => 'ok', 'msg' => 'Sessão encerrada'];
        $location = $_SESSION[User::USER_SESSION_DOUBLE] ? '/sessao_encerrada_por_duplicidade' : '/';
        session_destroy();
        return $this->respondWithData($response)->withStatus(302)->withHeader('Location', $location);
    }
}
