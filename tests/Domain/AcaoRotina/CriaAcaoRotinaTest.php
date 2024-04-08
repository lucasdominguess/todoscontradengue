<?php
declare(strict_types=1);
namespace Tests\Domain\AcaoRotina;
use App\Domain\AcaoRotina\AcaoRotina;
use App\Domain\AcaoRotina\AcaoRotinaException;
use \DateTime;
use Tests\TestCase;
require_once __DIR__ . '/../../../config.php';
$env = parse_ini_file(__DIR__ . '/../../../.env');

class CriaAcaoRotinaTest extends TestCase
{
    

    public function acaoProvider():array
    {
        $s = fn($date)=>$date->format('Y-m-d');
        return [
           'id_acao_numero_negativo'=> ['-1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1',AcaoRotinaException::INVALID_ID],
            'id_acao_como_texto'=>['a',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1',AcaoRotinaException::INVALID_ID],
            'data_acao_anterior_inicio_programa'=>[null,$s(new DateTime('2023-12-31',$GLOBALS['TZ'])),'1','1','1',AcaoRotinaException::SMALLER_DATA_ACAO],
            'data_acao_futura'=>[null,$s(new DateTime('tomorrow',$GLOBALS['TZ'])),'1','1','1',AcaoRotinaException::BIGGER_DATA_ACAO],
            'valor_texto_passado_para_data'=>[null,'a','1','1','1',AcaoRotinaException::INVALID_DATA_ACAO],
            'qde_casas_visitadas_como_texto'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'a','1','1',AcaoRotinaException::INVALID_QUANTAS_CASAS_VISITADAS],
            'qde_casas_visitadas_vazio'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'','1','1',AcaoRotinaException::INVALID_QUANTAS_CASAS_VISITADAS],
            'qde_casas_visitadas_como_numero_negativo'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'-1','1','1',AcaoRotinaException::INVALID_QUANTAS_CASAS_VISITADAS],
            'qde_casas_visitadas_como_nulo'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),null,'1','1',AcaoRotinaException::INVALID_QUANTAS_CASAS_VISITADAS],
            'qde_casas_com_criadouro_texto'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'1','a','1',AcaoRotinaException::INVALID_QUANTAS_CASAS_COM_CRIADOUROS],
            'qde_casas_com_criadouro_negativo'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'1','-1','1',AcaoRotinaException::INVALID_QUANTAS_CASAS_COM_CRIADOUROS],
            'qde_casas_com_criadouro_nulo'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'1',null,'1',AcaoRotinaException::INVALID_QUANTAS_CASAS_COM_CRIADOUROS],
            'qde_pessoas_orientadas_como_texto'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','a',AcaoRotinaException::INVALID_QUANTAS_PESSOAS_ORIENTADAS],
            'qde_pessoas_orientadas_como_negativo'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','-1',AcaoRotinaException::INVALID_QUANTAS_PESSOAS_ORIENTADAS],
            'qde_pessoas_orientadas_nulo'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'1','1',null,AcaoRotinaException::INVALID_QUANTAS_PESSOAS_ORIENTADAS],
            'numero_criadouros_maior_numero_casas'=>[null,$s(new DateTime('today',$GLOBALS['TZ'])),'1','2','1',AcaoRotinaException::INCONSISTENT_VISITAS_VERSUS_CRIADOUROS],
            
        ];
    }

    /**
     * @dataProvider  acaoProvider
     */
    public function testCriaAcao(?string $id, ?string $data_acao,?string $quantas_casas_visitadas, ?string $quantas_casas_com_criadouros, ?string $quantas_pessoas_orientadas, string $msg)
    {
        $this->expectExceptionMessage($msg);
        $this->expectException(AcaoRotinaException::class);

        AcaoRotina::create($id,$data_acao,$quantas_casas_visitadas,$quantas_casas_com_criadouros,$quantas_pessoas_orientadas);

    }
}
