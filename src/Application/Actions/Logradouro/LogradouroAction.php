<?php
declare(strict_types=1);
namespace App\Application\Actions\Logradouro;

use voku\helper\AntiXSS;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use App\Infrastructure\Persistence\LogradourosParaVisitar\LogradouroRepository;

abstract class LogradouroAction extends Action
{
    public function __construct(protected LoggerInterface $logger, protected LogradouroRepository $logradouroRepository, protected AntiXSS $antiXSS)
    {
             
        
        parent::__construct($logger);
        
    }
}
