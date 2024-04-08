<?php
// declare(strict_types=1);
namespace App\Application\Actions\AcoesRotina;
use App\Domain\AcaoRotina\AcaoRotina;
use Slim\Psr7\Response;

class SaveAcoesRotinaAction extends AcoesRotinaAction
{
    
    protected function action():Response
    {

        if (!isset($_POST)) {
            return $this->respondWithData(['cod'=>'fail', 'msg'=>'Nenhum dado foi recebido']);
        }

        if (!isset($_POST['id_rotina'])) {
            return $this->respondWithData(['cod'=>'fail', 'msg'=>'O identificador de rotina não foi recebido !']);
        }


        foreach ($_POST as $key => $value) {
            $_POST[$key] = $this->antiXSS->xss_clean($value);
        }

        
        $id_rotina = $_POST['id_rotina'] === '' ? null : $_POST['id_rotina'];
        $hoje = new \DateTime('today',$GLOBALS['TZ']);
        $data_acao = $hoje->format('Y-m-d');
        $data_acao = $_POST['data_acao'] ?? '';
        $quantas_casas_visitadas = $_POST['quantas_casas_visitadas'] ?? '';
        $equipe = $_POST['equipe'] ?? '';
        $quantas_casas_com_criadouros = $_POST['quantas_casas_com_criadouros'] ?? '';
        $quantas_pessoas_orientadas = $_POST['quantas_pessoas_orientadas'] ?? '';


        try {
            $acaoRotina = AcaoRotina::create($id_rotina,$data_acao,$quantas_casas_visitadas,$quantas_casas_com_criadouros,$quantas_pessoas_orientadas, $equipe);
        } catch (\Throwable $th) {
            return $this->respondWithData(['cod'=>'fail', 'msg'=>$th->getMessage()]);
        }

        
        return $this->respondWithData($this->acoesRotinaRepository->cadastrar($acaoRotina));

    }
}
