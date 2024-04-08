<?php
declare(strict_types=1);
namespace App\Application\Actions\Lancamento;

use Slim\Psr7\Response;
use App\Application\Actions\Lancamento\LancamentoAction;

class InsereLancamentoAction extends LancamentoAction
{


    private function decider($post):array
    {

        foreach ($post as $key => $value) {
            $post[$key] = $this->antiXSS->xss_clean($value);
        }

        



        return [];
    }
    protected function Action():Response
    {
        

        if (!isset($_POST)) {
            $res = ['cod'=>'fail','msg'=>'Nenhuma informação foi recebida para processamento'];
            return $this->respondWithData($res);
        }


            return $this->respondWithData($this->decider($_POST));
    }

}
