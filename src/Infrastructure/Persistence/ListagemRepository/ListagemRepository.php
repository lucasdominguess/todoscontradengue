<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\ListagemRepository;

use App\Domain\User\User;
use App\Infrastructure\Persistence\Sql\Sql;

class ListagemRepository
{
    function __construct(protected Sql $sql)
    {
        
    }

    public function listar():array
    {

        $cmd = "with ids_relacionados as(
            select id from users where id = :id_user or id_pai in(
            select id from users where id = :id_user or id_pai IN(
            select id from users where id = :id_user or id_pai in (
            select id from users where id = :id_user or id_pai = :id_user
            )))),
consolidado as (
 select lpv.id_user, lpv.sinan, lpv.quarteirao, lpv.logradouro,v.num_logradouro,v.complemento,case when v.necessidade_touca = 1 then 'Sim' when v.necessidade_touca = 0 then 'Não' else 'NA' end as necessidade_touca ,case when impossivel_remover_criadouro = 1 then 'Sim' when impossivel_remover_criadouro = 0 then 'Não' else 'NA' end as impossivel_remover_criadouro from logradouros_para_visitar lpv left join visitas v on v.id_logradouro = lpv.id            
where lpv.id_user in (select * from ids_relacionados)
)
select vhcu.user_cnes,vhcu.unidade,vhcu.uvis,vhcu.crs, consolidado.sinan,consolidado.quarteirao,consolidado.logradouro,consolidado.num_logradouro,consolidado.complemento,consolidado.necessidade_touca,consolidado.impossivel_remover_criadouro from consolidado join vw_hierarquia_com_uvis vhcu on vhcu.id = consolidado.id_user
";
    $stmt = $this->sql->prepare($cmd);
    $dados = [':id_user'=>$_SESSION[User::USER_ID]];
    
    $this->sql->setParams($stmt, $dados);
    
    try {
        $stmt->execute();
        return $stmt->fetchAll($this->sql::FETCH_ASSOC);
    } catch (\Throwable $th) {
        return [[]];
    }

    }
}
