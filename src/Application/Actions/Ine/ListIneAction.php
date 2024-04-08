<?php
declare(strict_types=1);
namespace App\Application\Actions\Ine;
use Slim\Psr7\Response;

class ListIneAction extends IneAction
{
    protected function action():Response
    {
        return $this->respondWithData($this->ineRepository->listar());
    }
}
