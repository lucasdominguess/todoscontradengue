<?php
declare(strict_types=1);
namespace App\Application\Actions\ScadenAction;

use Slim\Psr7\Response;
use App\Domain\User\User;

final class ListScadenAction extends ScadenAction
{
    protected function action():Response
    {   

        if ($_SESSION[User::USER_ROLE]!= 3) {
            return $this->respondWithData([[]])->withStatus(204);
        }

        return $this->respondWithData($this->scadenRepository->relatorio_scaden_por_uvis());
    }
}
