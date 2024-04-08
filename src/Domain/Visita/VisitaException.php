<?php
declare(strict_types=1);
namespace App\Domain\Visita;

final class VisitaException extends \Exception
{
   const ID_INVALIDO = "O valor esperado para o identificador do logradouro não é válido";
   const LOGRADOURO_INVALIDO = "O comprimento mínimo permitido para a informação de logradouro é: " . COMPRIMENTO_MINIMO_LOGRADOURO . ' caracteres';
   const DATA_VISITA_INVALIDA = 'A data da visita informada não é válida !';
   const DATA_VISITA_MAIOR_QUE_HOJE = "A data da visita não pode ser maior que hoje !";
   const DATA_VISITA_ANTERIOR_INICIO_PROGRAMA = "A data da visita não pode ser anterior à " . INICIO_PROGRAMA_FORMAT . ' !';
   const SINAN_INVALIDO = "O número do sinan não é válido !";
   const NUM_LOGRADOURO_INVALIDO = "O número informado para o logradouro não é válido !";
   const IMOVEL_NAO_VISITADO = "Por favor, informe se o imóvel foi visitado !";
   const IMOVEL_NAO_VISTORIADO = 'Informe se o imóvel foi vistoriado !';
   const AUSENCIA_INFORMACAO_CRIADOUROS_IDENTIFICADOS = "Informe se foram identificados criadouros !";
   const AUSENCIA_INFORMACAO_CRIADOUROS_ELIMINADOS = "Informe se foram eliminados os criadouros !";
   const AUSENCIA_INFORMACAO_NECESSIDADE_TOUCA = "Informe se há necessidade de touca para caixa d'agua !";
   const AUSENCIA_INFORMACAO_CRIADOUROS_IMPOSSIVEIS_REMOVER = "Informe se há criadouros impossíveis de remover !";
   const EQUIPE_INVALIDA = "O valor informado para a equipe não é válido !";

   function __construct($message = self::ID_INVALIDO, $code = 0, $previous = null)
   {
    parent::__construct($message, $code);
   }
}
