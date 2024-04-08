<?php
declare(strict_types=1);
namespace App\Application\Actions\Logradouro;
use Slim\Psr7\Response;
use App\Domain\User\User;
use App\Application\Actions\Logradouro\LogradouroAction;

final class ListaLogradouroAction extends LogradouroAction
{
    protected function action():Response
    {

        if ((int)$_SESSION[User::USER_ROLE] === 1) {
            $res = $this->logradouroRepository->listar_logradouros_users($_SESSION[User::USER_CNES]);
            $statuscode = $res[0]['sinan']? 200:204;
            return $this->respondWithData($res)->withStatus($statuscode);
        }

        if ((int)$_SESSION[User::USER_ROLE] === 2) {
            $res = $this->logradouroRepository->listar_logradouros($_SESSION[User::USER_CNES]);
            $statuscode = $res[0]['sinan']? 200:204;
            return $this->respondWithData($res)->withStatus($statuscode);
        }

        return $this->respondWithData([[]]);

        
    }
}
