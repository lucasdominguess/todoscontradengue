<?php
declare(strict_types=1);
namespace App\Application\Actions\AcoesRotina;

use App\Infrastructure\Persistence\AcoesRotina\AcoesRotinaRepository;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use voku\helper\AntiXSS;

abstract class AcoesRotinaAction extends Action
{
    function __construct(LoggerInterface $logger, protected AcoesRotinaRepository $acoesRotinaRepository, protected AntiXSS $antiXSS)
    {
        parent::__construct($logger);
    }
}
