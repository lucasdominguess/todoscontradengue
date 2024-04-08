<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Login;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserNotFoundException;
use App\Infrastructure\Persistence\Sql\Sql;
use GuzzleHttp\Client;
use Slim\Csrf\Guard;

class LoginRepository implements UserRepository
{
    /**
     * @var User[]
     */
    private array $users;

    /**
     * @param User[]|null $users
     */
    public function __construct(protected Sql $sql, protected Client $client)
    {
        
    }


    public function validarRecaptcha($token):array
    {

        global $env;
        $form_params = ['form_params'=>['secret'=>$env['google_secret_key'], 'response'=>$token]];
        $response = $this->client->post($env['google_url_validate_captcha'], $form_params);
        $statusCode = $response->getStatusCode();
        $body = (string)$response->getBody();
        return [$statusCode, $body];
    }
    public function logar(string $login, string $pass):User
    {
        global $env;
        $stmt = $this->sql->prepare('select * from users where user_login=:01');
        $dados = [':01'=> $login];
        $this->sql->setParams($stmt, $dados);
        $stmt->execute();
        $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);

        if (!isset($res[0]['id'])) {
            sleep(1);
            throw new \Exception("Usuário não encontrado ou senha inválida !", 1);
            
        }

        if (!password_verify($pass, $res[0]['user_password'])) {
            sleep(1);
            throw new \Exception("Usuário não encontrado ou senha inválida !", 1);
        }

        $user = new User($res[0]['id'],$res[0]['user_name'],$res[0]['user_admin']);
        $now = new \DateTime('now', $GLOBALS['TZ']);
        $now = $user->id . $now->format('Y-m-d H:i:s');
        $hash = password_hash($now, PASSWORD_DEFAULT);
        $redis = new \Redis();
        $redis->connect('172.30.28.183', 6379);
        $redis->set("{$env['app_prefix']}_{$user->id}",$hash);
        // print_r($res);
        return $user;
    }
}
