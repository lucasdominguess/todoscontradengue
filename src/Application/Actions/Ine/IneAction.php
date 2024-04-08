<?php
declare(strict_types=1);
namespace App\Application\Actions\Ine;
use App\Application\Actions\Action;
use App\Infrastructure\Persistence\IneRepository\IneRepository;


abstract class IneAction extends Action
{
    function __construct(\Psr\Log\LoggerInterface $logger, protected IneRepository $ineRepository)
    {

    }
}
