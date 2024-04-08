<?php
declare(strict_types=1);
namespace Tests\Domain\BoletimGestor;
use App\Domain\BoletimGestor\BoletimGestorException;
use \DateTime;
use Tests\TestCase;
use App\Domain\BoletimGestor\BoletimGestor;


final class BoletimGestorTest extends TestCase
{

    public function boletimGestorProvider():array
    {
        $s = fn(DateTime $data)=>$data->format('Y-m-d');
        return [
            'Id boletim invalido'=>['a',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','1','1','1',BoletimGestor::IDENTIFICADOR_BOLETIM_INVALIDO],
           'informado data futura'=> ['1',$s(new DateTime('tomorrow',$GLOBALS['TZ'])),'1','1','1','1','1','1',BoletimGestor::DATA_INFORMADA_MAIOR_HOJE],
           'Data invalida'=> ['1','a','1','1','1','1','1','1',BoletimGestor::DATA_INFORMADA_INVALIDA],
            'data anterior 01/01/2024'=>['1',$s(new DateTime('2023-12-31',$GLOBALS['TZ'])),'1','1','1','1','1','1',BoletimGestor::DATA_INFORMADA_ANTERIOR_PROGRAMA],
            'numero suspeitos como string'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'a','1','1','1','1','1',BoletimGestor::NUMERO_SUSPEITOS_INVALIDO],
            'numero suspeitos vazio'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'','1','1','1','1','1',BoletimGestor::NUMERO_SUSPEITOS_INVALIDO],
            'numero suspeitos negativo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'-1','1','1','1','1','1',BoletimGestor::NUMERO_SUSPEITOS_INVALIDO],
            'numero suspeitos nulo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),null,'1','1','1','1','1',BoletimGestor::NUMERO_SUSPEITOS_INVALIDO],

            'numero confirmados como string'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','a','1','1','1','1',BoletimGestor::NUMERO_CONFIRMADOS_INVALIDO],
            'numero confirmados vazio'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','','1','1','1','1',BoletimGestor::NUMERO_CONFIRMADOS_INVALIDO],
            'numero confirmados negativo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','-1','1','1','1','1',BoletimGestor::NUMERO_CONFIRMADOS_INVALIDO],
            'numero confirmados nulo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1',null,'1','1','1','1',BoletimGestor::NUMERO_CONFIRMADOS_INVALIDO],

            'numero total testes rapidos como string'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','a','1','1','1',BoletimGestor::TOTAL_TESTES_RAPIDOS_INVALIDO],
            'numero total testes rapidos vazio'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','','1','1','1',BoletimGestor::TOTAL_TESTES_RAPIDOS_INVALIDO],
            'numero total testes rapidos negativo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','-1','1','1','1',BoletimGestor::TOTAL_TESTES_RAPIDOS_INVALIDO],
            'numero total testes rapidos nulo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1',null,'1','1','1',BoletimGestor::TOTAL_TESTES_RAPIDOS_INVALIDO],

            'numero testes positivos como string'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','a','1','1',BoletimGestor::TOTAL_TESTES_POSITIVOS_INVALIDO],
            'numero testes positivos vazio'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','-1','1','1',BoletimGestor::TOTAL_TESTES_POSITIVOS_INVALIDO],
            'numero testes positivos negativo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','','1','1',BoletimGestor::TOTAL_TESTES_POSITIVOS_INVALIDO],
            'numero testes positivos nulo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1',null,'1','1',BoletimGestor::TOTAL_TESTES_POSITIVOS_INVALIDO],

            'estoque diario como string'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','1','a','1',BoletimGestor::ESTOQUE_DIARIO_INVALIDO],
            'estoque diario vazio'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','1','-1','1',BoletimGestor::ESTOQUE_DIARIO_INVALIDO],
            'estoque diario negativo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','1','','1',BoletimGestor::ESTOQUE_DIARIO_INVALIDO],
            'estoque diario nulo'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','1',null,'1',BoletimGestor::ESTOQUE_DIARIO_INVALIDO],

           'qde atendimentos como string'=> ['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','1','1','a',BoletimGestor::QDE_ATENDIMENTOS_INVALIDO],
           'qde atendimentos vazio'=> ['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','1','1','-1',BoletimGestor::QDE_ATENDIMENTOS_INVALIDO],
           'qde atendimentos negativo'=> ['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','1','1','',BoletimGestor::QDE_ATENDIMENTOS_INVALIDO],
           'qde atendimentos nulo'=> ['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','1','1',null,BoletimGestor::QDE_ATENDIMENTOS_INVALIDO],

            'casos_confirmados_maior_suspeitos'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','2','1','2','5','10',BoletimGestor::CASOS_CONFIRMADOS_MAIOR_SUSPEITOS],
            'testes_positivos_maior_realizados'=>['1',$s(new DateTime('today',$GLOBALS['TZ'])),'1','1','1','2','5','10',BoletimGestor::TESTES_POSITIVOS_MAIOR_REALIZADOS],
            
        ];
    }
    
    /**
     * @dataProvider boletimGestorProvider
     */
    public function testCriacao(?string $id,?string $data_declarada,?string $total_de_casos_atendidos_com_suspeita_de_dengue,?string $total_de_casos_atendidos_confirmados_para_dengue,?string $total_de_testes_rapido_de_dengue_realizados,?string $total_de_testes_rapido_de_dengue_positivos,?string $estoque_diario_dos_testes_rapidos_de_dengue,?string $total_de_atendimentos_realizados_pela_unidade,$msg)
    {
        $this->expectExceptionMessage($msg);
        $this->expectException(BoletimGestorException::class);
        BoletimGestor::create($id,$data_declarada,$total_de_casos_atendidos_com_suspeita_de_dengue,$total_de_casos_atendidos_confirmados_para_dengue,$total_de_testes_rapido_de_dengue_realizados,$total_de_testes_rapido_de_dengue_positivos,$estoque_diario_dos_testes_rapidos_de_dengue,$total_de_atendimentos_realizados_pela_unidade);
    }

}
