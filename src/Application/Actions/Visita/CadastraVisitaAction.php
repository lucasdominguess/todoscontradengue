<?php
declare(strict_types=1);
namespace App\Application\Actions\Visita;

use Slim\Psr7\Response;
use App\Domain\User\User;
use App\Domain\Visita\Visita;
use App\Application\Actions\Visita\VisitaAction;

class CadastraVisitaAction extends VisitaAction
{
    protected function action():Response
    {
        if (!isset($_POST)) {
            $res =  ['cod'=>'fail','msg'=>'Nenhum valor foi recebido. Operação abortada'];
            return $this->respondWithData($res);
        }


        foreach ($_POST as $key => $value) {
            $_POST[$key] = $this->antiXSS->xss_clean($value);
        }

        $hoje = new \DateTime('today', $GLOBALS['TZ']);

        $id_logradouro = $_POST['id_logradouro'] ?? null;
        $sinan = $_POST['sinan'] ?? null;
        $dataref = $hoje->format('Y-m-d');
        $cep_visita = $_POST['cep_visita'] ?? null;
        $logradouro = $_POST['logradouro'] ?? null;
        $num_logradouro = $_POST['num_logradouro'] ?? null;
        $complemento_logradouro = $_POST['complemento_logradouro'] ?? null;
        $imovel_visitado = $_POST['imovel_visitado'] ?? null;
        $imovel_vistoriado = $_POST['imovel_vistoriado'] ?? null;
        $identificado_criadouros = $_POST['identificado_criadouros'] ?? null;
        $eliminado_criadouros = $_POST['eliminado_criadouros'] ?? null;
        $necessidade_touca = $_POST['necessidade_touca'] ?? null;
        $criadouros_impossivel_remover = $_POST['criadouros_impossivel_remover'] ?? null;

        try {
            $visita = new Visita($id_logradouro,$sinan,$dataref,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);
        } catch (\Throwable $th) {
            $res =  ['cod'=>'fail','msg'=>$th->getMessage()];
            return $this->respondWithData($res);
        }
        $res =  ['cod'=>'fail','msg'=>'$th->getMessage()'];
        $res = $this->visitaRepository->cadastrar($_SESSION[User::USER_ID],$visita);
        return $this->respondWithData($res);

    }
}
