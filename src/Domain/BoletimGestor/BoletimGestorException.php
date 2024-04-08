<?php
declare(strict_types=1);
namespace App\Domain\BoletimGestor;

class BoletimGestorException extends \Exception
{
    function __construct($message = "", $code = 0, $previous = null)
    {
        parent::__construct($message);
    }
}
