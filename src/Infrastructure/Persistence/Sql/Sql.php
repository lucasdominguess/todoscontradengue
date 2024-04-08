<?php
namespace App\Infrastructure\Persistence\Sql;

class Sql extends \PDO
{
    function __construct()
    {
        global $env;
        parent::__construct("{$env['sgbd']}:dbname={$env['dbname']};host={$env['dbhost']}",$env['dbuser'], $env['dbpass']);
    }

    public function setParams(\PDOStatement $stmt, array $dados = []):void
    {
        foreach ($dados as $key => $value)
        {
            $stmt->bindValue($key, $value);
        }
    }
}
