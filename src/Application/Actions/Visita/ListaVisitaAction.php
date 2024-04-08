<?php
declare(strict_types=1);
namespace App\Application\Actions\Visita;
use Slim\Psr7\Response;

final class ListaVisitaAction extends VisitaAction
{
    protected function action():Response
    {


        return $this->respondWithData($this->visitaRepository->listar());

    }
}
