<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserNotFoundException;
use App\Infrastructure\Persistence\Sql\Sql;
use GuzzleHttp\Client;
use Slim\Csrf\Guard;

class InMemoryUserRepository implements UserRepository
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

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->users);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(int $id): User
    {
        if (!isset($this->users[$id])) {
            throw new UserNotFoundException();
        }

        return $this->users[$id];
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
        
        $stmt = $this->sql->prepare('select * from users where user_login=:01');
        $dados = [':01'=> $login];
        $this->sql->setParams($stmt, $dados);
        $stmt->execute();
        $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);
        // print_r($res);
        return $res;
    }
}
