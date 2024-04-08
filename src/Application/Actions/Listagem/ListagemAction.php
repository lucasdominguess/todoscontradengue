<?php
declare(strict_types=1);
namespace App\Application\Actions\Listagem;

use App\Application\Actions\Action;
use App\Infrastructure\Persistence\ListagemRepository\ListagemRepository;

abstract class ListagemAction extends Action
{
    function __construct(\Psr\Log\LoggerInterface $logger, protected ListagemRepository $listagemRepository){
        parent::__construct($logger);
    }
}
