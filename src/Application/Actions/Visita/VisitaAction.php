<?php
declare(strict_types=1);
namespace App\Application\Actions\Visita;

use App\Infrastructure\Persistence\Visita\VisitaRepository;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use voku\helper\AntiXSS;

abstract class VisitaAction extends Action
{
    function __construct(LoggerInterface $logger, protected VisitaRepository $visitaRepository, protected AntiXSS $antiXSS)
    {
        parent::__construct($logger);
    }
}
