<?php
declare(strict_types=1);
namespace App\Application\Actions\Sinan;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use App\Infrastructure\Persistence\Sinan\SinanRepository;

abstract class SinanAction extends Action
{
    public function __construct(protected LoggerInterface $logger, protected SinanRepository $sinanRepository)
    {
        parent::__construct($logger);
        
    }
}
