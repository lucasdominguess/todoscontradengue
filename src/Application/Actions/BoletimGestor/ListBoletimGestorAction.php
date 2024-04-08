<?php
declare(strict_types=1);
namespace App\Application\Actions\BoletimGestor;
use Slim\Psr7\Response;


class ListBoletimGestorAction extends BoletimGestorAction
{
    protected function action():Response
    {
        return $this->respondWithData($this->boletimGestorRepository->listar());
    }    
}
