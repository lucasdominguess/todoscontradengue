<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\IneRepository\IneRepository;
use Slim\Csrf\Guard;
use GuzzleHttp\Client;
use DI\ContainerBuilder;
use voku\helper\AntiXSS;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Sql\Sql;
use App\Infrastructure\Persistence\RedisConn\RedisConn;
use App\Infrastructure\Persistence\Login\LoginRepository;
use App\Infrastructure\Persistence\Lancamento\LancamentoRepository;
return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(LoginRepository::class),
        Guard::class => \DI\autowire(Guard::class),
        Sql::class => \DI\autowire(Sql::class),
        Client::class => \DI\autowire(Client::class),
        RedisConn::class => \DI\autowire(RedisConn::class),
        LancamentoRepository::class => \DI\autowire(LancamentoRepository::class),
        AntiXSS::class => \DI\autowire(AntiXSS::class),
        \PDO::class => \DI\autowire(Sql::class),
        IneRepository::class => \DI\autowire(IneRepository::class),
    ]);
};
