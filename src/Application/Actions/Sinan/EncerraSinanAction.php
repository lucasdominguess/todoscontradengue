<?php
declare(strict_types=1);
namespace App\Application\Actions\Sinan;
use Slim\Psr7\Response;
use App\Domain\User\User;
use App\Application\Actions\Sinan\SinanAction;


final class EncerraSinanAction extends SinanAction
{
    protected function action():Response
    {

        $id_user = (int)$_SESSION[User::USER_ID];
        $sinan = $_POST['sinan'] ?? null;
        $data_fim_bloqueio = $_POST['data_fim_bloqueio'] ?? null;
        $todos_quarteiroes_visitados = $_POST['todos_quarteiroes_visitados'] ?? null;

        if (!in_array($todos_quarteiroes_visitados,['0','1'])) {
            $res = ['cod'=>'fail','message'=>'Informe se todos os quarteirões foram visitados'];
            return $this->respondWithData($res);
        }

        $er = '/^\d{4}\-\d{2}\-\d{2}$/';

        if (!preg_match($er, $data_fim_bloqueio)) {
            $res = ['cod'=>'fail','message'=>'A data de encerramento informada não é válida'];
            return $this->respondWithData($res);
        }

        $hoje = new \DateTime('today',$GLOBALS['TZ']);
        $datacomp = new \DateTime($data_fim_bloqueio,$GLOBALS['TZ']);

        if ($datacomp > $hoje) {
            $res = ['cod'=>'fail','message'=>'A data de encerramento do sinan não pode ser superior à data de hoje'];
            return $this->respondWithData($res);
        }


        return $this->respondWithData($this->sinanRepository->cadastrarEncerramentoSinan($id_user, $sinan, $data_fim_bloqueio, (int)$todos_quarteiroes_visitados));

    }
}
