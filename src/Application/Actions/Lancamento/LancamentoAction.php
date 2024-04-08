<?php
declare(strict_types=1);
namespace App\Application\Actions\Lancamento;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use App\Infrastructure\Persistence\Lancamento\LancamentoRepository;
use voku\helper\AntiXSS;

abstract class LancamentoAction extends Action
{
    public function __construct(protected LoggerInterface $logger, protected LancamentoRepository $lancamentoRepository, protected AntiXSS $antiXSS)
    {
        parent::__construct($logger);

    }
}
