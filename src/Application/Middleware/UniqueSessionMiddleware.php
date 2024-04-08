<?php
namespace App\Application\Middleware;

use App\Infrastructure\Persistence\RedisConn\RedisConn;
use Slim\Psr7\Response;
use App\Domain\User\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class UniqueSessionMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  Request        $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    
    function __construct(protected RedisConn $redisConn)
    {
        
    }
     public function __invoke(Request $request, RequestHandler $handler): Response
    {
        global $env;
        if (!isset($_SESSION[User::USER_ID])) {
            $response = new Response();

            return $response->withHeader('Location', '/')->withStatus(302);
        }


        $hash = $this->redisConn->hget(APP_ID,$_SESSION[User::USER_LOGIN]);
        
        if ($_SESSION[User::USER_SESSION_HASH] != $hash) {
            
            $_SESSION[User::USER_SESSION_DOUBLE] = 1;
            $response = new Response();

            return $response->withHeader('Location', '/logout')->withStatus(302);
        }

        $response = $handler->handle($request);
        return $response;

    }
}