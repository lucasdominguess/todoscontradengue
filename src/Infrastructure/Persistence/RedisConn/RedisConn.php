<?php
namespace App\Infrastructure\Persistence\RedisConn;

final class RedisConn extends \Redis
{
    private string $host;
    private int $port;
    function __construct()
    {
        global $env;
        $this->host = $env['redis_host'];
        $this->port = (int)$env['redis_port'];
        $this->connect($this->host, $this->port);
    }
}
