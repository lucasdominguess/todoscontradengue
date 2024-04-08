<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\IneRepository;

use App\Domain\User\User;
use App\Infrastructure\Persistence\Sql\Sql;


class IneRepository
{
    function __construct(protected Sql $sql)
    {

    }

public function listar()
{
    $cnes = $_SESSION[User::USER_CNES] ?? '';

    $stmt = $this->sql->prepare('select * from ine where cmes = :cnes');
    $dados = [':cnes'=>$cnes];
    $this->sql->setParams($stmt, $dados);
    
    try {
        $stmt->execute();
        return $stmt->fetchAll(($this->sql::FETCH_ASSOC));
    } catch (\Throwable $th) {
        return [[]];
    }
}


}
