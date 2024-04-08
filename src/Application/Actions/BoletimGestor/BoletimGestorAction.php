<?php
declare(strict_types=1);
namespace App\Application\Actions\BoletimGestor;

use voku\helper\AntiXSS;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use App\Infrastructure\Persistence\BoletimGestor\BoletimGestorRepository;

abstract class BoletimGestorAction extends Action
{
    function __construct(LoggerInterface $logger, protected BoletimGestorRepository $boletimGestorRepository, protected AntiXSS $antiXSS)
    {
        parent::__construct($logger);
    }
}
