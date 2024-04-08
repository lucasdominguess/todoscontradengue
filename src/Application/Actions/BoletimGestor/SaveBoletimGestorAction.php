<?php
declare(strict_types=1);
namespace App\Application\Actions\BoletimGestor;
use App\Domain\BoletimGestor\BoletimGestor;
use Slim\Psr7\Response;

class SaveBoletimGestorAction extends BoletimGestorAction
{
    protected function action():Response
    {

        if (!isset($_POST)) {
            return $this->respondWithData(['cod'=>'fail', 'msg'=>'Nenhum dado foi recebido']);
        }

        if (!isset($_POST['id_monitoramento'])) {
            return $this->respondWithData(['cod'=>'fail', 'msg'=>'O identificador de lançamento não pôde ser avaliado !']);
        }

        $er = '/^\d*$/im';

        if (!preg_match($er,(string)$_POST['id_monitoramento'])) {
            return $this->respondWithData(['cod'=>'fail', 'msg'=>'O identificador de lançamento não é válidoxyz !']);
        }
        

        foreach ($_POST as $key => $value) {
            $_POST[$key] = $this->antiXSS->xss_clean($value);
        }

        $id = $_POST['id_monitoramento'] == '' ? null : $_POST['id_monitoramento'];
        $hoje = new \DateTime('today',$GLOBALS['TZ']);   
        $data_declarada = $hoje->format('Y-m-d');
        $data_declarada = $_POST['data_declarada'] ?? '';
        $total_de_casos_atendidos_com_suspeita_de_dengue = $_POST['total_de_casos_atendidos_com_suspeita_de_dengue'] ?? '';
        $total_de_casos_atendidos_confirmados_para_dengue = $_POST['total_de_casos_atendidos_confirmados_para_dengue'] ?? '';
        $total_de_testes_rapido_de_dengue_realizados = $_POST['total_de_testes_rapido_de_dengue_realizados'] ?? '';
        $total_de_testes_rapido_de_dengue_positivos = $_POST['total_de_testes_rapido_de_dengue_positivos'] ?? '';
        $estoque_diario_dos_testes_rapidos_de_dengue = $_POST['estoque_diario_dos_testes_rapidos_de_dengue'] ?? '';
        $total_de_atendimentos_realizados_pela_unidade = $_POST['total_de_atendimentos_realizados_pela_unidade'] ?? '';



        try {
            $boletimGestor = BoletimGestor::create($id,$data_declarada,$total_de_casos_atendidos_com_suspeita_de_dengue,$total_de_casos_atendidos_confirmados_para_dengue,$total_de_testes_rapido_de_dengue_realizados,$total_de_testes_rapido_de_dengue_positivos,$estoque_diario_dos_testes_rapidos_de_dengue,$total_de_atendimentos_realizados_pela_unidade);
        } catch (\Throwable $th) {
            return $this->respondWithData(['cod'=>'fail', 'msg'=>$th->getMessage()]);
        }


        return $this->respondWithData($this->boletimGestorRepository->salvar($boletimGestor));
    }
}
