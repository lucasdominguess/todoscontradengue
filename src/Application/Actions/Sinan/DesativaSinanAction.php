<?php
declare(strict_types=1);
namespace App\Application\Actions\Sinan;
use Slim\Psr7\Response;
use App\Domain\User\User;
use App\Application\Actions\Sinan\SinanAction;


final class DesativaSinanAction extends SinanAction
{
    protected function action():Response
    {

        $id_user = (int)$_SESSION[User::USER_ID];
        $sinan = $_POST['sinan'] ?? null;

        return $this->respondWithData($this->sinanRepository->desativa_sinan($sinan, $id_user));

    }
}
