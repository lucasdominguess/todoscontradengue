<?php
declare(strict_types=1);
namespace App\Application\Actions\Visita;

use Slim\Psr7\Response;
use App\Domain\User\User;

final class InformaEncerramentoVisitaAction extends VisitaAction
{
    protected function action():Response
    {

        $sinan = $_POST['sinan'] ?? '';
        $quarteirao = $_POST['quarteirao'] ?? '';
        $idlogradouro = $_POST['idlogradouro'] ?? '';
        $cnes = $_SESSION[User::USER_CNES];
        $res = $this->visitaRepository->informar_encerramento($sinan, $quarteirao,$cnes,$idlogradouro);
        return $this->respondWithData($res);
        
    }
}
