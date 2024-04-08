<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\ScadenRepository;

use App\Domain\User\User;
use App\Infrastructure\Persistence\Sql\Sql;

class ScadenRepository
{
    function __construct(private Sql $sql)
    {

    } 
    
    
    public function relatorio_scaden_por_uvis()
    {

        $cmd = "with ids_relacionados as(
            select id from users where id = :id_user or id_pai in(
            select id from users where id = :id_user or id_pai IN(
            select id from users where id = :id_user or id_pai in (
            select id from users where id = :id_user or id_pai = :id_user
            )))),
agregado as(
select
	lpv.sinan,
	lpv.logradouro,
	vhcu.unidade,
	vhcu.user_cnes,
	vhcu.crs, vhcu.uvis,
	v.data_visita_informada,
	lpv.data_fim_bloqueio,
	v.imovel_visitado,
	v.imovel_vistoriado
from
	logradouros_para_visitar lpv
left join visitas v on
	v.id_logradouro = lpv.id
join vw_hierarquia_com_uvis vhcu  on
	vhcu.id = lpv.id_user 
	where lpv.id_user in(select * from ids_relacionados)
)
select unidade,user_cnes, sinan,to_char(min(data_visita_informada),'DD/MM/YYYY') as primeira_visita,to_char(max(data_fim_bloqueio),'DD/MM/YYYY')  as encerramento_gestor,coalesce(sum(imovel_visitado),0) as imovel_visitado, coalesce(sum(imovel_vistoriado),0) as imovel_vistoriado
from agregado group by unidade,user_cnes,sinan";

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
