<?php
declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
@session_start();

use Slim\Csrf\Guard;
use Slim\Views\Twig;
use DI\ContainerBuilder;
use App\Domain\User\User;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;

//uses adicionais (após create project):
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Message\ServerRequestInterface;
use App\Application\Extensions\CsrfExtension;
use App\Application\Handlers\ShutdownHandler;
use Slim\Factory\ServerRequestCreatorFactory;
use App\Application\Handlers\HttpErrorHandler;
use App\Application\Settings\SettingsInterface;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Middleware\MyCustomErrorRenderer;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

$role = $_SESSION[User::USER_ROLE] ?? 0;
define('USER_ROLE',$role);

$qde_lancamentos_hoje = $_SESSION[User::USER_LANCOU_HOJE] ?? 0;
define('USER_QDE_LANCAMENTOS',$qde_lancamentos_hoje);

$username = $_SESSION[User::USER_NAME]?? '';
define('USUARIO_LOGADO',$username);

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    return $errstr;
}
set_error_handler("myErrorHandler");

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

$env = parse_ini_file(__DIR__ .'/../.env');


// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Twig
$twig = Twig::create(__DIR__ .'/../views', ['cache' => false]);
// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));
$guard = new Guard($responseFactory);
$twig->addExtension(new CsrfExtension($guard));

// Generate new tokens
function gerar_token()
{
    global $guard;
    $csrfNameKey = $guard->getTokenNameKey();
    $csrfValueKey = $guard->getTokenValueKey();
    $keyPair = $guard->generateToken();
}

gerar_token();

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Get the default error handler and register my custom error renderer.
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', MyCustomErrorRenderer::class);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
