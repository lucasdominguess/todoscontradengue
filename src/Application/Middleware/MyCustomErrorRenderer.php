<?php
namespace App\Application\Middleware;

use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class MyCustomErrorRenderer implements ErrorRendererInterface
{
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        return file_get_contents(__DIR__ . '/../../../views/404.html');
    }
}