<?php
declare(strict_types=1);
namespace App\Application\Actions\AcoesRotina;
use Slim\Psr7\Response;

class ListarAcoesRotina extends AcoesRotinaAction
{
    protected function action():Response{

        return $this->respondWithData($this->acoesRotinaRepository->buscarTodasAcoes());
    }
}
