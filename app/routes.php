<?php

declare(strict_types=1);

use App\Application\Actions\Ine\ListIneAction;
use Slim\App;
use Slim\Views\Twig;
use App\Application\Middleware\UserMiddleware;
use App\Application\Middleware\AdminMiddleware;
use App\Application\Actions\User\LoginUsersAction;
use App\Application\Actions\User\LogoutUsersAction;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Sinan\EncerraSinanAction;
use App\Application\Actions\Token\InvalidTokenAction;
use App\Application\Actions\Visita\ListaVisitaAction;
use App\Application\Actions\Sender\HandleSenderAction;
use App\Application\Actions\Sinan\DesativaSinanAction;
use App\Application\Middleware\ValidatePostMiddleware;
use App\Application\Middleware\UniqueSessionMiddleware;

//uses após create project:

use App\Infrastructure\Persistence\RedisConn\RedisConn;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\Actions\Listagem\FindListagemAction;
use App\Application\Actions\Visita\CadastraVisitaAction;
use App\Application\Actions\AcoesRotina\ListarAcoesRotina;
use App\Application\Actions\ScadenAction\ListScadenAction;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\Visita\ConsolidadoVisitaAction;
use App\Application\Actions\Logradouro\ListaLogradouroAction;
use App\Application\Actions\AcoesRotina\SaveAcoesRotinaAction;
use App\Application\Actions\Logradouro\InsereLogradouroAction;
use App\Application\Actions\BoletimGestor\ListBoletimGestorAction;
use App\Application\Actions\BoletimGestor\SaveBoletimGestorAction;
use App\Application\Actions\Visita\InformaEncerramentoVisitaAction;
global $resposeFactory;
return function (App $app)use ($resposeFactory) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'index.html',['hide_nav'=>1, 'hide_footer'=>1]);
    });
    $app->get('/sessao_encerrada_por_duplicidade', function (Request $request, Response $response) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'sessao_encerrada_por_duplicidade.html',['hide_nav'=>1]);
    });


    $app->post("/login",LoginUsersAction::class)->add(ValidatePostMiddleware::class);
    $app->get("/logout",LogoutUsersAction::class)->setName('logout');
    $app->get("/tokeninvalido",InvalidTokenAction::class);
    $app->get("/sender",HandleSenderAction::class);

    $app->group('/users', function (Group $group) {
        $group->get('/home', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'home_users.html');
        })->setName('home_users');
        
        $group->get('/registro_visita', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'registro_visita.html');
        })->setName('registro_visita');

        $group->get('/registro_rotina', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'registro_rotina.html');
        })->setName('registro_rotina');

        $group->get('/visitas', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'visitas.html');
        })->setName('visitas');
        $group->get('/monitor', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'monitor.html');
        })->setName('monitor_users');
        $group->get('/acessouser', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'home_ubs_user.html');
        });
        $group->get("/listar_logradouros",ListaLogradouroAction::class);
        $group->get("/listar_consolidado_visitas",ConsolidadoVisitaAction::class);
        $group->get("/listar_visitas",ListaVisitaAction::class);
        $group->post("/cadastrar_visita",CadastraVisitaAction::class);
        $group->post("/informa_encerramento_visitacao",InformaEncerramentoVisitaAction::class);
        $group->post("/cadastrar_acao_rotina",SaveAcoesRotinaAction::class);
        $group->get("/listar_acoes_rotina",ListarAcoesRotina::class);
        $group->get("/listar_ines",ListIneAction::class);


    })->add(new UserMiddleware());

    $app->group('/admin', function (Group $group) {
        $group->post("/cadastrar_logradouro",InsereLogradouroAction::class);
        $group->post("/cadastrar_encerramento_sinan",EncerraSinanAction::class);
        $group->post("/desativar_sinan",DesativaSinanAction::class);
        $group->post("/cadastrar_boletim_gestor",SaveBoletimGestorAction::class);
        $group->get("/listar_boletim_gestor",ListBoletimGestorAction::class);
        $group->get("/listar_scaden_uvis",ListScadenAction::class);
        $group->get("/relatorio_listagem",FindListagemAction::class);
        $group->get('/home', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'home_admin.html');
        })->setName('home_admin');

        $group->get('/rotinas', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'rotinas.html');
        })->setName('rotinas');

        $group->get('/scaden', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'scaden.html');
        })->setName('scaden');

        $group->get('/listagem', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'listagem.html');
        })->setName('listagem');

        $group->get('/registro_bloqueio', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'registro_bloqueio.html');
        })->setName('registro_bloqueio');
        $group->get('/monitor', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'monitor.html');
        })->setName('monitor');
        $group->get('/monitoramento_dengue', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'monitoramento_dengue.html');
        })->setName('monitoramento_dengue');
        $group->get('/monitoramento_dengue_view', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'monitoramento_dengue_view.html');
        })->setName('monitoramento_dengue_view');
        $group->get('/correcoes', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'correcoes.html');
        })->setName('correcoes');
        $group->get('/acessoadm', function (Request $request, Response $response) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'home_ubs_adm.html');
        });
    })->add(new AdminMiddleware());

    // -------------------------------
    
   

   
};
