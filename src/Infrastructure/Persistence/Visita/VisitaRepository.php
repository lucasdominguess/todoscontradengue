<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Visita;

use App\Domain\User\User;
use App\Domain\Visita\Visita;
use App\Infrastructure\Persistence\Sql\Sql;

class VisitaRepository
{
    function __construct(private Sql $sql)
    {

    }


    public function cadastrar(?int $id_user, Visita $visita): array
    {
        $this->sql->beginTransaction();

        $stmt = $this->sql->prepare('select count(*) as total from logradouros_para_visitar where id = :id_logradouro and data_fim_bloqueio is null');
        $dados = [':id_logradouro' => $visita->id_logradouro];

        $this->sql->setParams($stmt, $dados);
        $stmt->execute();
        $res = $stmt->fetch($this->sql::FETCH_ASSOC);

        if ((int) $res['total'] === 0) {
            $this->sql->rollBack();
            return ['cod' => 'fail', 'msg' => 'O sinan informado não existe ou já foi encerrado'];
        }



        $cmd = "INSERT INTO visitas(id_user,id_logradouro,data_visita_informada,num_logradouro,complemento,cep,imovel_visitado,imovel_vistoriado,identificado_criadouros,eliminado_criadouros,necessidade_touca,impossivel_remover_criadouro) VALUES (:id_user,:id_logradouro,to_date(:data_visita_informada,'YYYY-MM-DD'),:num_logradouro,:complemento,:cep,:imovel_visitado,:imovel_vistoriado,:identificado_criadouros,:eliminado_criadouros,:necessidade_touca,:impossivel_remover_criadouro)";
        $stmt = $this->sql->prepare($cmd);

        $dados = [
            ':id_user' => $id_user,
            ':id_logradouro' => $visita->id_logradouro,
            ':data_visita_informada' => $visita->dataref,
            ':num_logradouro' => $visita->num_logradouro,
            ':complemento' => $visita->complemento_logradouro,
            ':cep' => $visita->cep_visita,
            ':imovel_visitado' => $visita->imovel_visitado,
            ':imovel_vistoriado' => $visita->imovel_vistoriado,
            ':identificado_criadouros' => $visita->identificado_criadouros,
            ':eliminado_criadouros' => $visita->eliminado_criadouros,
            ':necessidade_touca' => $visita->necessidade_touca,
            ':impossivel_remover_criadouro' => $visita->criadouros_impossivel_remover
        ];

        $this->sql->setParams($stmt, $dados);


        try {
            $stmt->execute();
            $this->sql->commit();
            return ['cod' => 'ok', 'msg' => 'Visita cadastrada com sucesso !'];

        } catch (\Throwable $th) {
            $this->sql->rollBack();
            $errorCode = $stmt->errorCode();
            $msg = $errorCode == '23505' ? 'Um mesmo número de logradouro não pode ser visitado duas vezes no mesmo dia' : $th->getMessage();

            return ['cod' => 'fail', 'msg' => $msg];
        }


    }


    public function listar(): array
    {

        $id_user = $_SESSION[User::USER_ID] ?? "-1";
        $id_user = (string) $id_user;

        $cmd = "select
        lpv.sinan,
        lpv.logradouro,
        lpv.id_user as criador_logradouro,
        lpv.created_at,
        lpv.cnes as cnes_unidade,
        case when lpv.data_fim_bloqueio is null then 'Não finalizado' when lpv.todos_quarteiroes_visitados = 0 then 'NÃO' ELSE 'SIM' end as todos_quarteiroes_visitados,
        to_char(lpv.data_fim_bloqueio,'DD/MM/YYYY') as data_fim_bloqueio,
        lpv.quadra,
        lpv.observacoes,
        lpv.bairro,
        lpv.quarteirao,
        to_char(v.data_visita_informada,'DD/MM/YYYY') as data_visita_informada,
        v.num_logradouro,
        v.complemento,
        v.cep,
        case when v.imovel_visitado is null then 'NA' when imovel_visitado = 1 then 'SIM' when imovel_visitado = 0 then 'NÃO' else 'ND' end as imovel_visitado,
        case when v.imovel_vistoriado is null then 'NA' when imovel_vistoriado = 1 then 'SIM' when imovel_vistoriado = 0 then 'NÃO' else 'ND' end as imovel_vistoriado,
        case when v.identificado_criadouros is null then 'NA' when identificado_criadouros = 1 then 'SIM' when identificado_criadouros = 0 then 'NÃO' else 'ND' end as identificado_criadouros,
        case when v.eliminado_criadouros is null then 'NA' when eliminado_criadouros = 1 then 'SIM' when eliminado_criadouros = 0 then 'NÃO' else 'ND' end as eliminado_criadouros,
        case when v.necessidade_touca is null then 'NA' when necessidade_touca = 1 then 'SIM' when necessidade_touca = 0 then 'NÃO' else 'ND' end as necessidade_touca,
        case when v.impossivel_remover_criadouro is null then 'NA' when impossivel_remover_criadouro = 1 then 'SIM' when impossivel_remover_criadouro = 0 then 'NÃO' else 'ND' end as impossivel_remover_criadouro,
        u.user_name
        from visitas v
        join logradouros_para_visitar lpv on v.id_logradouro = lpv.id
        join users u on u.id = v.id_user";



        if ($id_user === "1") {
            $cmd .= ' ' . 'order by v.id desc limit 500;';
        } else {
            $cmd .= ' ' . "where v.id_user IN (select id from users where id_pai IN(
                select id from users where id_pai IN(
                select id from users where id = :id or id_pai = :id ) or id = :id
                ) or id = :id) or lpv.cnes = :cnes order by v.id desc limit 500;";
        }

        $stmt = $this->sql->prepare($cmd);
        $cnes = preg_match('/^\d+$/', (string) $_SESSION[User::USER_CNES]) ? $_SESSION[User::USER_CNES] : '';

        if ($id_user != "1") {
            $dados = [':id' => $id_user, ':cnes' => (string) $cnes];
            $this->sql->setParams($stmt, $dados);
        }


        try {
            $stmt->execute();
            return $stmt->fetchAll($this->sql::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [[]];
        }



    }


    function consolidado_visitas($id_user)
    {
        // $cmd = "with cnes_relacionados as (
        //     select sub.user_cnes from (
        //     select * from users where  id = :id_user or id_pai IN(
        //     select id from users where id_pai IN(
        //     select id from users where id = :id_user or id_pai = :id_user
        //     )or id = :id_user
        //     ) order by 1
        //     ) sub where sub.user_cnes is not null
        //     ),
        //     n1 as(
        //     select lpv.cnes,lpv.sinan,lpv.quarteirao,lpv.logradouro,count(v.id_logradouro) as total_visitas from logradouros_para_visitar lpv
        //     left join visitas v on v.id_logradouro = lpv.id
        //     where cnes in (select * from cnes_relacionados)
        //     group by lpv.cnes,lpv.sinan,lpv.quarteirao,lpv.logradouro
        //     order by 1 desc limit 500
        //     ),
        //     n2 as (select distinct cnes,sinan,data_fim_bloqueio,unidade_informou_termino_visitacao from logradouros_para_visitar lpv where lpv.cnes in (select * from cnes_relacionados)),
        //     cnes_selecionados as(
        //     select n1.cnes,n1.sinan,n1.quarteirao,n1.logradouro,n1.total_visitas, n2.data_fim_bloqueio,n2.unidade_informou_termino_visitacao from n1 join n2
        //     using(sinan)
        //     )
        //     select cs.cnes,hie.unidade,hie.crs ,hie.sts,hie.uvis,cs.sinan,cs.quarteirao,cs.logradouro,cs.total_visitas,cs.data_fim_bloqueio,case when cs.unidade_informou_termino_visitacao = 1 then 'Sim' else 'Não' end as unidade_informou_termino_visitacao from cnes_selecionados cs join vw_hierarquia_com_uvis hie on cs.cnes = hie.user_cnes and user_role = 1";


        $cmd = "with cnes_relacionados as (
            select sub.user_cnes from (
            select * from users where  id = :id_user or id_pai IN(
            select id from users where id_pai IN(
            select id from users where id = :id_user or id_pai = :id_user
            )or id = :id_user
            ) order by 1
            ) sub where sub.user_cnes is not null
            ),
            n1 as(
            select lpv.cnes,lpv.sinan,lpv.quarteirao,lpv.logradouro,lpv.data_fim_bloqueio,case when lpv.unidade_informou_termino_visitacao = 1 then 'Sim' else 'Não' end as unidade_informou_termino_visitacao,count(v.id_logradouro) as total_visitas from logradouros_para_visitar lpv
            left join visitas v on v.id_logradouro = lpv.id
            where cnes in (select * from cnes_relacionados)
            group by lpv.cnes,lpv.sinan,lpv.quarteirao,lpv.logradouro,lpv.unidade_informou_termino_visitacao,lpv.data_fim_bloqueio
            order by 1 desc limit 500
            ) select n1.cnes,vhcu.unidade,vhcu.crs,vhcu.sts,vhcu.uvis ,n1.sinan,n1.quarteirao,n1.logradouro,n1.total_visitas,n1.data_fim_bloqueio,n1.unidade_informou_termino_visitacao from n1 join vw_hierarquia_com_uvis vhcu on n1.cnes = vhcu.user_cnes and vhcu.user_role = 1";

        $stmt = $this->sql->prepare($cmd . ' order by sinan asc');
        $dados = [':id_user'=>$id_user];
        $this->sql->setParams($stmt, $dados);

        try {
            $stmt->execute();
            return $stmt->fetchAll($this->sql::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [[]];
        }
    }


    public function informar_encerramento(string $sinan, string $quarteirao, string $cnes, string $idlogradouro):array
    {
        $this->sql->beginTransaction();
        $cmd = 'select count(*) as total from logradouros_para_visitar WHERE sinan = :sinan AND cnes = :cnes AND quarteirao = :quarteirao and id = :idlogradouro AND unidade_informou_termino_visitacao = 0';
        $stmt = $this->sql->prepare($cmd);
        $dados = [':sinan'=>$sinan,':cnes'=>$cnes,':quarteirao'=>$quarteirao,':idlogradouro'=>$idlogradouro];
        $this->sql->setParams($stmt, $dados);

        try {
            $stmt->execute();
            $res = $stmt->fetch($this->sql::FETCH_ASSOC);
            if ((int)$res['total'] === 0) {
                $this->sql->rollBack();
                return ['cod'=>'fail','msg'=>'O logradouro informado já foi encerrado ou não existe !'];
            }
        } catch (\Throwable $th) {
            $this->sql->rollBack();
            return ['cod'=>'fail','msg'=>'Não foi possível verificar os quarteirões nesse momento. Por favor, tente mais tarde'];
        }
        
        
        $cmd = "UPDATE logradouros_para_visitar SET unidade_informou_termino_visitacao = 1 WHERE sinan = :sinan AND cnes = :cnes AND quarteirao = :quarteirao and id = :idlogradouro AND unidade_informou_termino_visitacao = 0";
        $stmt = $this->sql->prepare($cmd);
        $dados = [':sinan'=>$sinan,':cnes'=>$cnes,':quarteirao'=>$quarteirao,':idlogradouro'=>$idlogradouro];
        $this->sql->setParams($stmt, $dados);


        try {
            $stmt->execute();
            $this->sql->commit();
            return ['cod'=>'ok', 'msg'=>"Lançamento efetuado com sucesso.{$stmt->rowCount()} registro(s) atualizado(s)"];
        } catch (\Throwable $th) {
            $this->sql->rollBack();
            return ['cod'=>'fail','msg'=>'Não foi possível efetuar a atualização dos quarteirões nesse momento. Por favor, tente mais tarde'];
        }


    }


}
