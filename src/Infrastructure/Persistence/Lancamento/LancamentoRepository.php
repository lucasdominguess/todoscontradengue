<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Lancamento;

use App\Domain\User\User;
use App\Infrastructure\Persistence\Sql\Sql;

final class LancamentoRepository
{
    function __construct(protected Sql $sql)
    {

    }



    public function lancamentos_hoje():int
    {

        $unidade = $_SESSION[User::USER_ID];

        $stmt = $this->sql->prepare("select count(*) as total from logradouros_para_visitar where id_user = :01 and to_char(dataref,'YYYY-MM-DD') = :02");
        $dataref = date_create('now', $GLOBALS['TZ']);
        $hoje = $dataref->format('Y-m-d');
        $dados = [':01'=>$_SESSION[User::USER_ID], ':02'=>$hoje];
        $this->sql->setParams($stmt, $dados);
        $stmt->execute();
        $res = $stmt->fetch($this->sql::FETCH_ASSOC);
        return (int)$res['total'];
    }

    


}
