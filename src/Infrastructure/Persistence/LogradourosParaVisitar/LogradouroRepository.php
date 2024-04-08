<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\LogradourosParaVisitar;

use App\Infrastructure\Persistence\Sql\Sql;
use voku\helper\AntiXSS;

final class LogradouroRepository
{
    function __construct(private Sql $sql, private AntiXSS $antiXSS)
    {

    }

    private function atualizar(string $id_logradouro,string $sinan, string $logradouro,int $id_user ,string $cnes,?string $quadra, ?string $observacoes, ?string $bairro, ?string $quarteirao):array
    {
        $dataref = new \DateTime('today',$GLOBALS['TZ']);
        $stmt = $this->sql->prepare("select id from logradouros_para_visitar lpv where lpv.id = :id and lpv.dataref = to_date(:dataref,'YYYY-MM-DD')");

        $dados = [':id'=>$id_logradouro, ':dataref'=>$dataref->format('Y-m-d')];
        $this->sql->setParams($stmt, $dados);
        $stmt->execute();
        $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);

        if (!isset($res[0]['id'])) {
            return ['cod'=>'fail','msg'=>'A atualização solicitada não corresponde à data atual ou não existe'];
        }

        $stmt = $this->sql->prepare("select count(*) as total from visitas v where v.id_logradouro = :id");
        $dados = [':id'=>$id_logradouro];
        $this->sql->setParams($stmt, $dados);
        $stmt->execute();
        $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);

        if (!isset($res[0]['total'])) {
            return ['cod'=>'fail','msg'=>'Não foi possível verificar a ocorrência de visitas para a atualização solicitada'];
        }

        $total = (int)$res[0]['total'];

        if ($total > 0) {
            return ['cod'=>'fail','msg'=>'Não é possível editar um logradouro que já tenha recebido visitas'];
        }

        $stmt = $this->sql->prepare("update logradouros_para_visitar set sinan = :sinan,logradouro = :logradouro,quarteirao = :quarteirao where id = :id");
        $dados = [':sinan'=>$sinan,':logradouro'=>$logradouro,':quarteirao'=>$quarteirao,':id'=>$id_logradouro];
        $this->sql->setParams($stmt, $dados);


        try {
            $stmt->execute();
            return ['cod'=>'ok','msg'=>'Atualização processada com sucesso !'];
        } catch (\Throwable $th) {
            $error = $stmt->errorCode();
            $msg = $error == '23505' ? 'Logradouro e quarteirão já cadastrados para esse Sinan !' : $th->getMessage();
            return ['cod'=>'fail','msg'=>$msg];
        }

        

    }


    public function cadastrarLogradouroParaVisitar(string $id_logradouro,string $sinan, string $logradouro,int $id_user ,string $cnes,?string $quadra, ?string $observacoes, ?string $bairro, ?string $quarteirao):Array
    {

        
        if ($id_logradouro !== '') {
            return $this->atualizar($id_logradouro,$sinan,$logradouro,$id_user,$cnes,$quadra,$observacoes,$bairro,$quarteirao);
            
        }

        $stmt = $this->sql->prepare('INSERT INTO logradouros_para_visitar (sinan,logradouro,id_user,cnes,quadra,observacoes,bairro,quarteirao) VALUES (:sinan,:logradouro,:id_user,:cnes,:quadra,:observacoes,:bairro,:quarteirao)');
        $dados = [':sinan'=>$sinan,':logradouro'=>$logradouro,':id_user'=>$id_user,':cnes'=>$cnes,':quadra'=>$quadra,':observacoes'=>$observacoes,':bairro'=>$bairro,':quarteirao'=>$quarteirao];
        $this->sql->setParams($stmt, $dados);
        
        try {
            $stmt->execute();
            return ['cod'=>'ok', 'msg'=>'Inserido com sucesso'];
        } catch (\Throwable $th) {
            $error = $stmt->errorCode();
            $msg = $error == '23505' ? 'Logradouro e quarteirão já cadastrados para esse Sinan !' : $th->getMessage();
            return ['cod'=>'fail','msg'=>$msg];
        }

    }



    public function listar_logradouros(string $cnes):array
    {

        $cmd = "with n1 as(
            select lpv.sinan,lpv.quarteirao,lpv.logradouro,count(v.id_logradouro) as total_visitas from logradouros_para_visitar lpv
            left join visitas v on v.id_logradouro = lpv.id
            where lpv.cnes = :cnes
            group by lpv.sinan,lpv.quarteirao,lpv.logradouro
            ),
            n2 as (select distinct sinan,data_fim_bloqueio,unidade_informou_termino_visitacao from logradouros_para_visitar lpv where lpv.cnes = :cnes)
            
            select n1.sinan,n1.quarteirao,n1.logradouro,n1.total_visitas, n2.data_fim_bloqueio,case when unidade_informou_termino_visitacao = 1 then 'Sim' else 'Não' end as unidade_informou_termino_visitacao from n1 join n2
            using(sinan)";


            $cmd = "select lpv.id,lpv.dataref,lpv.sinan,lpv.quarteirao,lpv.logradouro,case when lpv.unidade_informou_termino_visitacao = 1 then 'SIM' else 'NÃO' end as unidade_informou_termino_visitacao,lpv.data_fim_bloqueio,count(v.id_logradouro) as total_visitas from logradouros_para_visitar lpv
            left join visitas v on v.id_logradouro = lpv.id
            where cnes = :cnes
            group by lpv.id,lpv.dataref,lpv.sinan,lpv.quarteirao,lpv.logradouro,lpv.unidade_informou_termino_visitacao,lpv.data_fim_bloqueio";

        $stmt = $this->sql->prepare($cmd . ' order by sinan asc');
        $dados = [':cnes'=>$cnes];
        $this->sql->setParams($stmt, $dados);

        try {
            $stmt->execute();
            return $stmt->fetchAll($this->sql::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [[]];
        }

    }


    public function listar_logradouros_users(string $cnes, int $somente_ativos = 1):array
    {

        $cmd = $somente_ativos == 0 ? 'select * from logradouros_para_visitar where cnes = :cnes and data_fim_bloqueio is null':'select * from logradouros_para_visitar where cnes = :cnes and ativo = 1 and data_fim_bloqueio is null';
        $stmt = $this->sql->prepare($cmd . ' order by sinan asc');
        $dados = [':cnes'=>$cnes];
        $this->sql->setParams($stmt, $dados);

        try {
            $stmt->execute();
            return $stmt->fetchAll($this->sql::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [[]];
        }

    }


   


}
