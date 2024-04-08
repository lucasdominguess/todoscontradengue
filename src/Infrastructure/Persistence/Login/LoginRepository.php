<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Login;

use App\Domain\User\User;
use App\Infrastructure\Persistence\RedisConn\RedisConn;
use GuzzleHttp\Client;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Sql\Sql;



final class LoginRepository implements UserRepository
{

    function __construct(private Sql $sql, private Client $client, protected RedisConn $redisConn)
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

    private function inserir_novo_usuario($ldap_mail, $ldap_name, $ldap_login)
    {
        $stmt = $this->sql->prepare("INSERT INTO users (user_email,user_name,user_login) values (:01, :02, :03) on conflict(user_login) do nothing");
        $dados = [':01' => $ldap_mail, ':02' => $ldap_name, ':03' => $ldap_login];
        $this->sql->setParams($stmt, $dados);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if (!$rows > 0) {
            throw new \Exception("Não foi possível cadastrar o usuário! Por favor, tente novamente.", 1);

        }
    }


    
    public function buscar_ldap(string $user, string $pass):User
    {
        global $env;
        
        $conn = @ldap_connect($env['LDAP_HOST']);
        if (!$conn) {
            throw new \Exception("Falha na comunicação com o servidor! Tente novamente.");
        }

        

        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        $ldap = @ldap_bind($conn, $env['LDAP_DOMAIN'] . "\\" . $user, $pass);
        if (!$ldap) {
            
            throw new \Exception("Usuário ou senha inválidos! Verifique suas credenciais.");
        }


        $search = ldap_search($conn, $env['LDAP_BASE'], "sAMAccountName=$user");
        $info = ldap_get_entries($conn, $search);
        $ldap_mail = $info[0]['mail'][0]; //email
        $ldap_name = $info[0]['cn'][0]; //nome do usuário
        $ldap_login = $info[0]['samaccountname'][0]; //login de rede
        $stmt = $this->sql->prepare('select * from users where user_login = :01');
        $data = [':01' => $user];
        $this->sql->setParams($stmt, $data);
        $stmt->execute();
        $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);

        if (!isset($res[0]['user_login'])) {
            $this->inserir_novo_usuario($ldap_mail, $ldap_name, $ldap_login);
            
        }

        $stmt = $this->sql->prepare('select * from users where user_login = :01');
        $data = [':01' => $user];
        $this->sql->setParams($stmt, $data);
        $stmt->execute();
        $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);

        if (!isset($res[0]['id'])) {
            throw new \Exception("Não foi possível criar o usuário", 1);
            
        }

        $user = new User($res[0]['id'],$res[0]['user_name'],$res[0]['user_role'], $res[0]['user_cnes']);
        return $user;

    }

    public function buscar_local(string $login, string $pass):User
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

        $user = new User($res[0]['id'],$res[0]['user_name'],$res[0]['user_role'],$res[0]['user_cnes']);
        return $user;
    }
    public function logar(string $user_login, string $pass):User
    {
        // $er = '/^(x|d)\d{6}$/i';
        
        // if (preg_match($er, $user_login)) {
        //     $user_login = mb_strtolower($user_login);
        //     return $this->buscar_ldap($user_login,$pass);
        // }

        return $this->buscar_local($user_login,$pass);
        
    }


    public function listar_usuarios():array{
        $stmt = $this->sql->query('SELECT * FROM users order by user_name;');
        $res = $stmt->fetchAll($this->sql::FETCH_ASSOC);
        return $res;
    }

    public function ativar_usuario(int $id_usuario, int $novo_status):array
    {

        $this->sql->beginTransaction();
        $stmt = $this->sql->prepare('UPDATE users SET user_active = :01 WHERE id = :02');
        $dados = [':01'=>$novo_status, ':02'=>$id_usuario];
        $this->sql->setParams($stmt, $dados);
        $stmt->execute();
        $stmt = $this->sql->prepare('SELECT * FROM users WHERE id = :01');
        $dados = [':01'=>$id_usuario];
        $this->sql->setParams($stmt, $dados);
        $stmt->execute();
        try {
            $this->sql->commit();
            $res = $stmt->fetch($this->sql::FETCH_ASSOC);
            return $res;
        } catch (\Throwable $th) {
            return [];
        }
    }
}
