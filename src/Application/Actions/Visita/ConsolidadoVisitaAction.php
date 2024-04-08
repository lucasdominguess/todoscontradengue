<?php
declare(strict_types=1);
namespace App\Application\Actions\Visita;
use Slim\Psr7\Response;
use App\Domain\User\User;

final class ConsolidadoVisitaAction extends VisitaAction
{
    protected function action():Response
    {


        return $this->respondWithData($this->visitaRepository->consolidado_visitas((int)$_SESSION[User::USER_ID]));

    }
}
