<?php
declare(strict_types=1);
namespace App\Domain\Visita;
use \DateTime;
use \Exception;

class Visita
{
    function __construct(public readonly ?string $id_logradouro,public readonly ?string $sinan,
    public readonly ?string $dataref,
    public readonly ?string $cep_visita,public readonly ?string $logradouro,
    public readonly ?string $num_logradouro,public readonly ?string $complemento_logradouro,
    public readonly ?string $imovel_visitado,public readonly ?string $imovel_vistoriado,
    public readonly ?string $identificado_criadouros,public readonly ?string $eliminado_criadouros,
    public readonly ?string $necessidade_touca,public readonly ?string $criadouros_impossivel_remover)
    {

        $this->check_id_logradouro();
        $this->confirmDate();
        $this->confere_sinan();
        $this->check_logradouro();
        $this->check_num_logradouro();
        $this->check_imovel_visitado();
        $this->check_imovel_vistoriado();
        $this->identificado_criadouros();
        $this->eliminado_criadouros();
        $this->necessidade_touca();
        $this->criadouros_impossivel_remover();

        
    }



    private function check_id_logradouro()
    {
        $er = "/^\d+$/m";

        if (!preg_match($er, (string)$this->id_logradouro)) {
            throw new VisitaException(VisitaException::ID_INVALIDO);
            
        }
    }

    private function confirmDate()
    {
        $er = '/^\d{4}\-\d{2}\-\d{2}$/';

        if (!preg_match($er, (string)$this->dataref)) {
            throw new VisitaException(VisitaException::DATA_VISITA_INVALIDA, 1);
            
        }

        $compare = new DateTime($this->dataref,$GLOBALS['TZ']);
        
        $hoje = new DateTime('today', $GLOBALS['TZ']);
        $inicio_programa = new DateTime($GLOBALS['INICIO_PROGRAMA'], $GLOBALS['TZ']);

        if ($compare > $hoje) {
            throw new VisitaException(VisitaException::DATA_VISITA_MAIOR_QUE_HOJE, 1);
        }
        if ($compare < $inicio_programa) {
            throw new VisitaException(VisitaException::DATA_VISITA_ANTERIOR_INICIO_PROGRAMA, 1);
        }

        


    }

    private function confere_sinan()
{
    $er = '/^\d{7}$/';

    if (!preg_match($er, (string)$this->sinan)) {
        throw new VisitaException(VisitaException::SINAN_INVALIDO, 1);
        
    }
}


private function check_logradouro()
{
    if (mb_strlen(str_replace(' ','', (string)$this->logradouro))< COMPRIMENTO_MINIMO_LOGRADOURO) {
        throw new VisitaException(VisitaException::LOGRADOURO_INVALIDO, 1);
        
    }
}


private function check_num_logradouro()
{
    $er = '/^\d+$/m';

    if (!preg_match($er, (string)$this->num_logradouro)) {
        throw new VisitaException(VisitaException::NUM_LOGRADOURO_INVALIDO, 1);
        
    }
}

private function check_boolean(?string $value):bool
{
    $bools = ['0','1',null];

    return in_array($value, $bools);

}

private function check_imovel_visitado()
{
    if (!$this->check_boolean($this->imovel_visitado) || $this->imovel_visitado === null) {
        throw new VisitaException(VisitaException::IMOVEL_NAO_VISITADO, 1);
        
    }
}

private function check_imovel_vistoriado()
{
    if ($this->imovel_visitado =='0') {
        return;
    }

    if (!in_array($this->imovel_vistoriado,['0','1'])) {
        throw new VisitaException(VisitaException::IMOVEL_NAO_VISTORIADO, 1);
        
    }
}

private function identificado_criadouros()
{
    if ($this->imovel_vistoriado === '0' || $this->imovel_vistoriado === null) {
        return;
    }

    if (!in_array($this->identificado_criadouros,['0','1'])) {
        throw new VisitaException(VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IDENTIFICADOS, 1);
    }
}

private function eliminado_criadouros()
{
    if ($this->identificado_criadouros === '0' || $this->identificado_criadouros === null) {
        return;
    }

    if (!in_array($this->eliminado_criadouros,['0','1'])) {
        throw new VisitaException(VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_ELIMINADOS, 1);
    }

}
private function necessidade_touca()
{
    if ($this->identificado_criadouros === '0' || $this->identificado_criadouros === null) {
        return;
    }

    if (!in_array($this->necessidade_touca,['0','1'])) {
        throw new Exception(VisitaException::AUSENCIA_INFORMACAO_NECESSIDADE_TOUCA, 1);
    }

}
private function criadouros_impossivel_remover()
{
    if ($this->identificado_criadouros === '0' || $this->identificado_criadouros === null) {
        return;
    }

    if (!in_array($this->criadouros_impossivel_remover,['0','1'])) {
        throw new VisitaException(VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IMPOSSIVEIS_REMOVER, 1);
    }

}

}

