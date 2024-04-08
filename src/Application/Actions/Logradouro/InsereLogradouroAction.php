<?php
declare(strict_types=1);
namespace App\Application\Actions\Logradouro;
use Slim\Psr7\Response;
use App\Domain\User\User;
use App\Application\Actions\Logradouro\LogradouroAction;

final class InsereLogradouroAction extends LogradouroAction
{
    protected function action():Response
    {

        $sinan = $_POST['sinan'] ?? null;
        $logradouro = $_POST['logradouro'] ?? null;
        $quarteirao = $_POST['quarteirao'] ?? null;
        $quadra = $_POST['quadra'] ?? null;
        $observacoes = $_POST['observacoes'] ?? null;
        $bairro = $_POST['bairro'] ?? null;
        $id_user = $_SESSION[User::USER_ID];
        $cnes = $_SESSION[User::USER_CNES];


        if (!isset($_POST['id_logradouro'])) {
            $res = ['cod'=>'fail','msg'=>'Não foi possível verificar o tipo de lançamento'];
            return $this->respondWithData($res)->withStatus(200);
        }

        $id_logradouro = $_POST['id_logradouro'];


        $er = '/^\d{7}$/'; 

        if (!preg_match($er, $sinan)) {
            $res = ['cod'=>'fail','msg'=>'O número do sinan deve ter 7 caracteres numéricos'];
            return $this->respondWithData($res)->withStatus(200);
        }


        if (mb_strlen(str_replace(' ','',$logradouro)) < COMPRIMENTO_MINIMO_LOGRADOURO) {
            $res = ['cod'=>'fail','msg'=>'O comprimento mínimo para o logradouro é de: ' . COMPRIMENTO_MINIMO_LOGRADOURO . ' caracteres'];
            return $this->respondWithData($res)->withStatus(200);
            
        }

        $logradouro = $this->antiXSS->xss_clean($logradouro);
        $logradouro = mb_strtoupper(trim($logradouro));

        $quarteirao = $this->antiXSS->xss_clean($quarteirao);
        $quarteirao = mb_strtoupper($quarteirao);
        $er = '/^\d{3}[a-z]{1}\d{6}$/im';
        if (!preg_match($er, $quarteirao)) {
            $res = ['cod'=>'fail','msg'=>'O formato do quarteirão é inválido'];
            return $this->respondWithData($res)->withStatus(200);
            
        }

        

        
        
        
        $res = $this->logradouroRepository->cadastrarLogradouroParaVisitar($id_logradouro,$sinan, $logradouro,$id_user, $cnes, $quadra, $observacoes, $bairro, $quarteirao);
        return $this->respondWithData($res);
        

    }
}
