<?php
declare(strict_types=1);
namespace App\Domain\AcaoRotina;

use App\Domain\AcaoRotina\AcaoRotina;
use App\Domain\DomainException\DomainException;



final class AcaoRotinaException extends \Exception
{
    const INVALID_ID = 'O identificador da ação não é válido !';
    const INVALID_DATA_ACAO = 'A data informada para a ação não é válida !';
    const BIGGER_DATA_ACAO = 'A data informada para a ação não pode ser maior que hoje !';
    const SMALLER_DATA_ACAO = 'A data informada para a ação não pode ser anterior à ' . INICIO_PROGRAMA_FORMAT . ' !';
    const INVALID_QUANTAS_CASAS_VISITADAS = 'O valor informado para a quantidade de casas visitadas não é válido !';
    const INVALID_QUANTAS_CASAS_COM_CRIADOUROS = 'O valor informado para a quantidade de casas com criadouros não é válido !';
    const INVALID_QUANTAS_PESSOAS_ORIENTADAS = 'O valor informado para a quantidade de pessoas orientadas não é válido !';
    const INCONSISTENT_VISITAS_VERSUS_CRIADOUROS = 'O número de casas com criadouros não pode ser maior que o  número de casas visitadas !';
    const INVALID_EQUIPE = 'A equipe informada não é válida !';
    function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
