<?php
declare(strict_types=1);
namespace App\Domain\AcaoRotina;
use \DateTime;
class AcaoRotina
{

    
    private function __construct(public readonly ?int $id, public readonly DateTime $data_acao,public readonly int $quantas_casas_visitadas, public readonly int $quantas_casas_com_criadouros, public readonly int $quantas_pessoas_orientadas, public readonly int $equipe)
    {

        $this->verificar_consistencia();

    }


    
   
    /**
     * @throws AcaoRotinaException
     */
    private function verificar_consistencia():void
    {

        if ($this->quantas_casas_com_criadouros > $this->quantas_casas_visitadas) {
            throw new AcaoRotinaException(AcaoRotinaException::INCONSISTENT_VISITAS_VERSUS_CRIADOUROS);
            
        }
    }

    public static function create(?string $id, ?string $data_acao,?string $quantas_casas_visitadas, ?string $quantas_casas_com_criadouros, ?string $quantas_pessoas_orientadas, ?string $equipe)
    {


        return new self(self::set_id($id),self::set_data_acao($data_acao),self::set_quantas_casas_visitadas($quantas_casas_visitadas),self::set_quantas_casas_com_criadouros($quantas_casas_com_criadouros),self::set_quantas_pessoas_orientadas($quantas_pessoas_orientadas), self::set_equipe($equipe));


    }


private static function set_equipe(?string $equipe)
{
    $er = '/^\d+$/';

    if (!preg_match($er, $equipe)) {
        throw new AcaoRotinaException(AcaoRotinaException::INVALID_EQUIPE);
        
    }

    return (int)$equipe;
}

private static function set_id(?string $id):?int
{
    if ($id === null || $id === '') {
        return null;
    }

    if (!is_numeric($id) || !preg_match('/^\d+$/',(string)$id)) {
        throw new AcaoRotinaException(AcaoRotinaException::INVALID_ID);
        
    }
    
    return (int)$id;
}
private static function set_data_acao(?string $data_acao):DateTime
{
    
    if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/',(string)$data_acao)) {
        throw new AcaoRotinaException(AcaoRotinaException::INVALID_DATA_ACAO);
        
    }

    $hoje = new DateTime('today',$GLOBALS['TZ']);
    $data_acao = new DateTime($data_acao,$GLOBALS['TZ']);

    if ($data_acao > $hoje) {
        throw new AcaoRotinaException(AcaoRotinaException::BIGGER_DATA_ACAO);
        
    }

    $dataref = new DateTime($GLOBALS['INICIO_PROGRAMA'],$GLOBALS['TZ']);

    if ($data_acao < $dataref) {
        throw new AcaoRotinaException(AcaoRotinaException::SMALLER_DATA_ACAO);
        
    }
    
    return $data_acao;
}
private static function set_quantas_casas_visitadas(?string $quantas_casas_visitadas):int
{
    
    if (!is_numeric($quantas_casas_visitadas) || !preg_match('/^\d+$/',(string)$quantas_casas_visitadas)) {
        throw new AcaoRotinaException(AcaoRotinaException::INVALID_QUANTAS_CASAS_VISITADAS);
        
    }
    
    return (int)$quantas_casas_visitadas;
}
private static function set_quantas_casas_com_criadouros(?string $quantas_casas_com_criadouros):int
{
    if (!is_numeric($quantas_casas_com_criadouros) || !preg_match('/^\d+$/',(string)$quantas_casas_com_criadouros)) {
        throw new AcaoRotinaException(AcaoRotinaException::INVALID_QUANTAS_CASAS_COM_CRIADOUROS);
        
    }
    
    return (int)$quantas_casas_com_criadouros;
}
private static function set_quantas_pessoas_orientadas(?string $quantas_pessoas_orientadas):int
{
    if (!is_numeric($quantas_pessoas_orientadas) || !preg_match('/^\d+$/',(string)$quantas_pessoas_orientadas)) {
        throw new AcaoRotinaException(AcaoRotinaException::INVALID_QUANTAS_PESSOAS_ORIENTADAS);
        
    }
    
    return (int)$quantas_pessoas_orientadas;
}

}
