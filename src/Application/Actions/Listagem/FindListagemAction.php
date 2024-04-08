<?php
declare(strict_types=1);
namespace App\Application\Actions\Listagem;

use Slim\Psr7\Response;

class FindListagemAction extends ListagemAction
{
    protected function action():Response
    {
        return $this->respondWithData($this->listagemRepository->listar());
    }
}
