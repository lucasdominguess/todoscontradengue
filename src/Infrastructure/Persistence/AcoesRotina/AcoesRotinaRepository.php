<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\AcoesRotina;

use App\Domain\User\User;
use App\Domain\AcaoRotina\AcaoRotina;
use App\Infrastructure\Persistence\Sql\Sql;

class AcoesRotinaRepository
{
    function __construct(protected Sql $sql)
    {

    }

    private function verificar_permissao(int $id):bool
    {

        $hoje = new \DateTime('today',$GLOBALS['TZ']);
        $stmt = $this->sql->prepare("select count(*) as total from acao_de_rotina adr where adr.id = :id and adr.user_cnes = :cnes and data_acao = :hoje");
        $dados = [':id'=>$id, ':cnes'=>$_SESSION[User::USER_CNES],':hoje'=>$hoje->format("Y-m-d")];

        $this->sql->setParams($stmt, $dados);

        $stmt->execute();
        $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);

        if (!isset($res[0]['total'])) {
            return false;
        }

        $total = (int)$res[0]['total'];
        
        return $total > 0;
    }


    public function cadastrar(AcaoRotina $acaoRotina):array
    {

        if ($acaoRotina->id !== null) {
            $verificar_permissao = $this->verificar_permissao($acaoRotina->id);

            if (!$verificar_permissao) {
                return ['cod'=>'fail', 'msg'=>'O item solicitado não existe ou você não tem permissão para editá-lo !'];
            }
        }


        $cmd = "insert into acao_de_rotina(id,data_acao,quantas_casas_visitadas,quantas_casas_com_criadouros,quantas_pessoas_orientadas,user_cnes,id_user,id_ine) values(primary_key,:data_acao,:quantas_casas_visitadas,:quantas_casas_com_criadouros,:quantas_pessoas_orientadas,:user_cnes,:id_user,:equipe) ON CONFLICT (id) DO UPDATE SET quantas_casas_visitadas = :quantas_casas_visitadas,quantas_casas_com_criadouros = :quantas_casas_com_criadouros,quantas_pessoas_orientadas = :quantas_pessoas_orientadas, id_ine = :equipe";
        $dados = [':id_rotina'=>$acaoRotina->id,':data_acao'=>$acaoRotina->data_acao->format('Y-m-d'),':quantas_casas_visitadas'=>$acaoRotina->quantas_casas_visitadas,':quantas_casas_com_criadouros'=>$acaoRotina->quantas_casas_com_criadouros,':quantas_pessoas_orientadas'=>$acaoRotina->quantas_pessoas_orientadas,':user_cnes'=>$_SESSION[User::USER_CNES],':id_user'=>$_SESSION[User::USER_ID],':equipe'=>$acaoRotina->equipe];

        if ($acaoRotina->id === null) {
            unset($dados[':id_rotina']);
            $cmd = str_replace('primary_key',"nextval('acao_de_rotina_id_seq'::regclass)",$cmd);
        }else{
            $cmd = str_replace('primary_key',':id_rotina',$cmd);
        }


        $stmt = $this->sql->prepare($cmd);
        $this->sql->setParams($stmt, $dados);

        try {
            $stmt->execute();
            return ['cod'=>'ok', 'msg'=>'Ação registrada com sucesso'];
        } catch (\Throwable $th) {
            if ($stmt->errorCode()=='23505') {
                return ['cod'=>'fail', 'msg'=>'Um único lançamento diário é permitido !'];
            }
            if ($stmt->errorCode()==='22003') {
                return ['cod'=>'fail', 'msg'=>'Um dos valores informados ultrapassa o limite permitido de 32.768'];
            }
            return ['cod'=>'fail', 'msg'=>$th->getMessage()];
        }


    }


    public function buscarAcao(string $user_cnes, string $data_acao):array
    {
        $stmt = $this->sql->prepare('select * from acao_de_rotina where user_cnes = :user_cnes and data_acao = :data_acao');
        $dados = [':user_cnes'=>$user_cnes,':data_acao'=>$data_acao];
        $this->sql->setParams($stmt, $dados);

        try {
            $stmt->execute();
            $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);
            return $res;
        } catch (\Throwable $th) {
            return [[]];
        }


    }


    private function decider():\PDOStatement
    {
        $user_role = $_SESSION[User::USER_ROLE];

        if ($user_role == '2') {
            $stmt =$this->sql->prepare( "select adr.id_ine,ine.ine,adr.id,vhcu.unidade,vhcu.crs,vhcu.sts,vhcu.uvis, to_char(adr.data_acao,'DD/MM/YYYY') as data_acao, adr.quantas_casas_visitadas, adr.quantas_casas_com_criadouros, adr.quantas_pessoas_orientadas, adr.user_cnes, adr.id_user, to_char(adr.created_at,'DD/MM/YYYY HH24:MI') as criado_em from acao_de_rotina adr join vw_hierarquia_com_uvis vhcu on vhcu.id = adr.id_user left join ine using(id_ine) where adr.user_cnes = :user_cnes order by 1 desc limit 1000");
            $dados = [':user_cnes'=>$_SESSION[User::USER_CNES]];
            $this->sql->setParams($stmt, $dados);
            return $stmt;

        }


        $stmt = $this->sql->prepare("with cnes_relacionados as(
            select user_cnes from users where id = :id_user or id_pai in(
            select id from users where id = :id_user or id_pai IN(
            select id from users where id = :id_user or id_pai in (
            select id from users where id = :id_user or id_pai = :id_user
            ))))
            select adr.id_ine,ine.ine,adr.id,vhcu.unidade,vhcu.crs,vhcu.sts,vhcu.uvis, to_char(adr.data_acao,'DD/MM/YYYY') as data_acao, adr.quantas_casas_visitadas, adr.quantas_casas_com_criadouros, adr.quantas_pessoas_orientadas, adr.user_cnes, adr.id_user, to_char(adr.created_at,'DD/MM/YYYY HH24:MI') as criado_em from acao_de_rotina adr join vw_hierarquia_com_uvis vhcu on vhcu.id = adr.id_user left join ine using(id_ine) where adr.user_cnes in(select * from cnes_relacionados) order by 1 desc limit 1000");

            $dados = [':id_user'=>$_SESSION[User::USER_ID]];
            $this->sql->setParams($stmt, $dados);
            return $stmt;
            
    }


    public function buscarTodasAcoes():array
    {
        $stmt = $this->decider();

        try {
            $stmt->execute();
            return $stmt->fetchAll($this->sql::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [[]];
        }
    }
}
