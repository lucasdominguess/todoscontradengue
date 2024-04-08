<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Lancamento\LancamentoRepository;
use App\Infrastructure\Persistence\RedisConn\RedisConn;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{

    public function __construct(protected LoggerInterface $logger, protected UserRepository $userRepository, protected RedisConn $redisConn, protected LancamentoRepository $lancamentoRepository)
    {
        parent::__construct($logger);
        
    }
}
