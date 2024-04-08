<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\BoletimGestor;

use App\Domain\User\User;
use App\Domain\BoletimGestor\BoletimGestor;
use App\Infrastructure\Persistence\Sql\Sql;

class BoletimGestorRepository
{
    function __construct(protected Sql $sql)
    {

    }


    private function verificar_permissao($id):bool
    {
        $hoje = new \DateTime('today', $GLOBALS['TZ']);
        $stmt = $this->sql->prepare("select count(*) as total from boletim_gestor bg where bg.id = :id and bg.user_cnes = :cnes and data_declarada = :hoje");

        $dados = [':id'=>$id, ':cnes'=>$_SESSION[User::USER_CNES], ':hoje'=>$hoje->format('Y-m-d')];
        $this->sql->setParams($stmt, $dados);

        $stmt->execute();
        $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);


        if (!isset($res[0]['total'])) {
            return false;
        }

        $total = (int)$res[0]['total'];

        return $total > 0;
    }


    public function salvar(BoletimGestor $boletimGestor):array
    {
        //id,data_declarada,total_de_casos_atendidos_com_suspeita_de_dengue,total_de_casos_atendidos_confirmados_para_dengue,total_de_testes_rapido_de_dengue_realizados,total_de_testes_rapido_de_dengue_positivos,estoque_diario_dos_testes_rapidos_de_dengue,total_de_atendimentos_realizados_pela_unidade,user_id,user_cnes,datahora,ativo

        if ($boletimGestor->id !== null) {
            $verificar_permissao = $this->verificar_permissao($boletimGestor->id);
            if (!$verificar_permissao) {
                return ['cod'=>'fail', 'msg'=>'O item solicitado não existe ou você não tem permissão para editá-lo !'];
            }
        }

        $cmd = "insert into boletim_gestor(id,data_declarada,total_de_casos_atendidos_com_suspeita_de_dengue,total_de_casos_atendidos_confirmados_para_dengue,total_de_testes_rapido_de_dengue_realizados,total_de_testes_rapido_de_dengue_positivos,estoque_diario_dos_testes_rapidos_de_dengue,total_de_atendimentos_realizados_pela_unidade,user_id,user_cnes)values(primary_key,to_date(:data_declarada,'YYYY-MM-DD'),:total_de_casos_atendidos_com_suspeita_de_dengue,:total_de_casos_atendidos_confirmados_para_dengue,:total_de_testes_rapido_de_dengue_realizados,:total_de_testes_rapido_de_dengue_positivos,:estoque_diario_dos_testes_rapidos_de_dengue,:total_de_atendimentos_realizados_pela_unidade,:user_id,:user_cnes) ON CONFLICT (id) DO update set total_de_casos_atendidos_com_suspeita_de_dengue = :total_de_casos_atendidos_com_suspeita_de_dengue,total_de_casos_atendidos_confirmados_para_dengue = :total_de_casos_atendidos_confirmados_para_dengue,total_de_testes_rapido_de_dengue_realizados = :total_de_testes_rapido_de_dengue_realizados,total_de_testes_rapido_de_dengue_positivos = :total_de_testes_rapido_de_dengue_positivos,estoque_diario_dos_testes_rapidos_de_dengue = :estoque_diario_dos_testes_rapidos_de_dengue,total_de_atendimentos_realizados_pela_unidade = :total_de_atendimentos_realizados_pela_unidade returning id";

        
            if($boletimGestor->id === null) {
                $cmd = str_replace('primary_key',"nextval('boletim_gestor_id_seq'::regclass)",$cmd);
            }else{
                $cmd = str_replace('primary_key',":id",$cmd);
            }


        $stmt = $this->sql->prepare($cmd);

        $dados = [':id'=>$boletimGestor->id,':data_declarada'=>$boletimGestor->data_declarada->format('Y-m-d'),':total_de_casos_atendidos_com_suspeita_de_dengue'=>$boletimGestor->total_de_casos_atendidos_com_suspeita_de_dengue,':total_de_casos_atendidos_confirmados_para_dengue'=>$boletimGestor->total_de_casos_atendidos_confirmados_para_dengue,':total_de_testes_rapido_de_dengue_realizados'=>$boletimGestor->total_de_testes_rapido_de_dengue_realizados,':total_de_testes_rapido_de_dengue_positivos'=>$boletimGestor->total_de_testes_rapido_de_dengue_positivos,':estoque_diario_dos_testes_rapidos_de_dengue'=>$boletimGestor->estoque_diario_dos_testes_rapidos_de_dengue,':total_de_atendimentos_realizados_pela_unidade'=>$boletimGestor->total_de_atendimentos_realizados_pela_unidade,':user_id'=>$_SESSION[User::USER_ID],':user_cnes'=>$_SESSION[User::USER_CNES]];

        if ($boletimGestor->id === null) {
            unset($dados[':id']);
        }

        $this->sql->setParams($stmt, $dados);


        try {
            $stmt->execute();
            $res = $stmt->fetch($this->sql::FETCH_ASSOC);

            if (!isset($res['id'])) {
                return ['cod'=>'fail', 'msg'=>'Não foi possível efetuar o cadastro no momento. Por favor, tente mais tarde.'];
            }
            return ['cod'=>'ok', 'msg'=>'Cadastro efetuado com sucesso !'];



        } catch (\Throwable $th) {
            if ($stmt->errorCode()==='23505') {
                return ['cod'=>'fail', 'msg'=>'Um único lançamento por data é permitido.'];
            }
            if ($stmt->errorCode()==='22003') {
                return ['cod'=>'fail', 'msg'=>'Um dos valores informados ultrapassa o limite permitido de 32.768'];
            }

            return ['cod'=>'fail', 'msg'=>'Um erro inesperado foi retornado. Se o problema persistir, contate o administrador' . $th->getMessage()];
        }



    }


    public function listar()
    {

        $cmd = "with ids_relacionados as(
            select id from users where id = :id_user or id_pai in(
            select id from users where id = :id_user or id_pai IN(
            select id from users where id = :id_user or id_pai in (
            select id from users where id = :id_user or id_pai = :id_user
            ))))
            select
            bg.id,
            vhcu.unidade,
            vhcu.crs,
            vhcu.sts,
            vhcu.uvis,
            to_char(bg.data_declarada,'DD/MM/YYYY')as data_informada,
            bg.total_de_casos_atendidos_com_suspeita_de_dengue,
            bg.total_de_casos_atendidos_confirmados_para_dengue,
            bg.total_de_testes_rapido_de_dengue_realizados,
            bg.total_de_testes_rapido_de_dengue_positivos,
            bg.estoque_diario_dos_testes_rapidos_de_dengue,
            bg.total_de_atendimentos_realizados_pela_unidade,
            bg.user_id,
            bg.user_cnes,
            to_char(bg.datahora,'DD/MM/YYYY HH24:MI') as criado_em,
            bg.ativo
            from
            boletim_gestor bg join vw_hierarquia_com_uvis vhcu on bg.user_id = vhcu.id where user_id in (select * from ids_relacionados)";

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
