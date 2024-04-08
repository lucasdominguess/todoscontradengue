<?php
declare(strict_types=1);
namespace App\Application\Actions\ScadenAction;

use App\Infrastructure\Persistence\ScadenRepository\ScadenRepository;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;

abstract class ScadenAction extends Action
{
    function __construct(LoggerInterface $logger, protected ScadenRepository $scadenRepository)
    {
        parent::__construct($logger);
    }

}
