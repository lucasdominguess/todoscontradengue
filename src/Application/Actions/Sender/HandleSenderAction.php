<?php
namespace App\Application\Actions\Sender;

use Slim\Psr7\Response;
use App\Domain\User\User;

final class HandleSenderAction extends SenderAction
{
    protected function Action():Response
    {
        
        global $env;
        if (!isset($_SESSION[User::USER_ID])) {
            # code...
            return $this->response->withHeader("Location","/")->withStatus(302);
        }

        // $redis = new \Redis();
        // $redis->connect($env['redis_host'], $env['redis_port']);
        // $redis->hset(APP_ID,$_SESSION[User::USER_LOGIN], $_SESSION[User::USER_SESSION_HASH]);
        // unset($redis);
        switch ($_SESSION[User::USER_ROLE]) {
        case 1:
            return $this->response->withHeader("Location","/users/home")->withStatus(302);
        case 2:
            return $this->response->withHeader("Location","/admin/home")->withStatus(302);
    }

        if ($_SESSION[User::USER_ROLE]>2) {
            return $this->response->withHeader("Location","/admin/monitor")->withStatus(302);
        }

    return $this->response->withHeader("Location","/")->withStatus(302);
}

}