<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\User;
use Psr\Http\Message\ResponseInterface as Response;

class LoginUsersAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {

        global $env;

        $login = $_POST['user_login'] ?? null;
        $password = $_POST['user_password'] ?? null;
        $g_recaptcha_response = $_POST['g-recaptcha-response'] ?? null;

        // if ($g_recaptcha_response === null) {
        //     $response = ['cod' => 'fail', 'msg' => 'Token recaptcha não recebido ou inválido !'];
        //     return $this->respondWithData($response);
        // }

        if ($login === null || $password === null) {
            $response = ['cod' => 'fail', 'msg' => 'Informe os dados de acesso'];
            return $this->respondWithData($response);
        }

        // [$statusCode, $body] = $this->userRepository->validarRecaptcha($g_recaptcha_response);


        // if ($statusCode !== 200) {
        //     $response = ['cod' => 'fail', 'msg' => 'Não foi possível validar o recaptcha. Por favor, tente mais tarde.'];
        //     return $this->respondWithData($response);
        // }

        // $body = json_decode($body, true);

        // if ($body['success'] !== true) {
        //     $response = ['cod' => 'fail', 'msg' => 'Token expirado.'];
        //     return $this->respondWithData($response);
        // }


        $now = new \DateTime('now',$GLOBALS['TZ']);
        if (!isset($_SESSION['tentativas'])) {
            $_SESSION['tentativas'] = 0;
            $_SESSION['proxima_tentativa'] = $now;
        }

        $_SESSION['tentativas'] ++;
        $seconds = $_SESSION['tentativas'] * 5;
        $proxima_tentativa = date_add(new \DateTime('now',$GLOBALS['TZ']),new \DateInterval("PT{$seconds}S"));

        
       
        if ($_SESSION['tentativas'] === 3) {

            $_SESSION['proxima_tentativa'] = $proxima_tentativa;
            $response = ['cod' => 'fail', 'msg' => "Devido à falhas consecutivas no login você deve aguardar até: {$_SESSION['proxima_tentativa']->format('d/m/Y H:i:s')} para tentar novamente !"];
            return $this->respondWithData($response);
        }

        if ($_SESSION['tentativas'] > 3 && $_SESSION['proxima_tentativa'] > $now) {

            $_SESSION['proxima_tentativa'] = $proxima_tentativa;
            $_SESSION['refresh_token'] = 1;
            $response = ['cod' => 'fail', 'msg' => "Devido à falhas consecutivas de login você deve aguardar até: {$_SESSION['proxima_tentativa']->format('d/m/Y H:i:s')} para tentar novamente !"];
            return $this->respondWithData($response);
        }

        
        try {
            $user = $this->userRepository->logar($login, $password);
        } catch (\Throwable $th) {
            $response = ['cod' => 'fail', 'msg' => $th->getMessage()];
            return $this->respondWithData($response);
        }
        
        unset($_SESSION['proxima_tentativa']);
        unset($_SESSION['tentativas']);

        session_regenerate_id();
        $_SESSION[User::USER_NAME] = $user->user_name;
        $_SESSION[User::USER_ID] = $user->id;
        $_SESSION[User::USER_ROLE] = $user->user_role;
        $_SESSION[User::USER_CNES] = $user->user_cnes;
        $_SESSION[User::USER_LOGIN] = $login;
        $agora = new \DateTime('now', $GLOBALS['TZ']);
        $agora = (string)$user->id . $agora->format('dmYH:i:s');
        $_SESSION[User::USER_SESSION_HASH] = md5(password_hash($agora, PASSWORD_DEFAULT));
        //xxxxxxxxxxxxx

        // $redis = new \Redis();
        // $redis->connect($env['redis_host'], (int)$env['redis_port']);

        try {
            $this->redisConn->hset(APP_ID,$_SESSION[User::USER_LOGIN], $_SESSION[User::USER_SESSION_HASH]);
        } catch (\Throwable $th) {
            $response = ['cod' => 'fail', 'msg' => $th->getMessage()];
            return $this->respondWithData($response);
        }

        try {
            $lancamentos_hoje = $this->lancamentoRepository->lancamentos_hoje();
        } catch (\Throwable $th) {
            $response = ['cod' => 'fail', 'msg' => $th->getMessage()];
            return $this->respondWithData($response);
        }
        
        $_SESSION[User::USER_LANCOU_HOJE] = $lancamentos_hoje;
        
        //xxxxxxxxxxxxx
        $response = ['cod' => 'ok', 'msg' => "Acessando...."];
        return $this->respondWithData($response);
    }
}
