<?php
declare(strict_types=1);
namespace Tests\Domain\Visita;
use Tests\TestCase;
use App\Domain\Visita\Visita;
use App\Domain\Visita\VisitaException;
require_once __DIR__ . '/../../../config.php';
$env = parse_ini_file(__DIR__ . '/../../../.env');

class CriaVisitaTest extends TestCase
{
      public function visitaProvider():array
      {
        $s = fn($date)=>$date->format('Y-m-d');
return [
['a','1234567',$s(new \DateTime('today')),null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::ID_INVALIDO],
['-1','1234567',$s(new \DateTime('today')),null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::ID_INVALIDO],
[null,'1234567',$s(new \DateTime('today')),null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::ID_INVALIDO],
['','1234567',$s(new \DateTime('today')),null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::ID_INVALIDO],
['1','1234567','z',null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::DATA_VISITA_INVALIDA],
['1','1234567',$s(new \DateTime('tomorrow')),null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::DATA_VISITA_MAIOR_QUE_HOJE],
['1','1234567',$s(new \DateTime('2023-12-31')),null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::DATA_VISITA_ANTERIOR_INICIO_PROGRAMA],
['1','123456',$s(new \DateTime('today')),null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::SINAN_INVALIDO],
['1','12345678',$s(new \DateTime('today')),null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::SINAN_INVALIDO],
['1','1234a67',$s(new \DateTime('today')),null,'logradouro','12',null,'1','1','1','1','1','1',VisitaException::SINAN_INVALIDO],
['1','1234567',$s(new \DateTime('today')),null,'a','12',null,'1','1','1','1','1','1',VisitaException::LOGRADOURO_INVALIDO],
['1','1234567',$s(new \DateTime('today')),null,'abc d','12',null,'1','1','1','1','1','1',VisitaException::LOGRADOURO_INVALIDO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','a',null,'1','1','1','1','1','1',VisitaException::NUM_LOGRADOURO_INVALIDO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','-1',null,'1','1','1','1','1','1',VisitaException::NUM_LOGRADOURO_INVALIDO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro',null,null,'1','1','1','1','1','1',VisitaException::NUM_LOGRADOURO_INVALIDO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,null,'1','1','1','1','1',VisitaException::IMOVEL_NAO_VISITADO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'a','1','1','1','1','1',VisitaException::IMOVEL_NAO_VISITADO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'-1','1','1','1','1','1',VisitaException::IMOVEL_NAO_VISITADO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'2','1','1','1','1','1',VisitaException::IMOVEL_NAO_VISITADO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1',null,'1','1','1','1',VisitaException::IMOVEL_NAO_VISTORIADO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','a','1','1','1','1',VisitaException::IMOVEL_NAO_VISTORIADO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','2','1','1','1','1',VisitaException::IMOVEL_NAO_VISTORIADO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','-1','1','1','1','1',VisitaException::IMOVEL_NAO_VISTORIADO],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1',null,'1','1','1',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IDENTIFICADOS],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','-1','1','1','1',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IDENTIFICADOS],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','a','1','1','1',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IDENTIFICADOS],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','2','1','1','1',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IDENTIFICADOS],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','a','1','1',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_ELIMINADOS],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1',null,'1','1',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_ELIMINADOS],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','-1','1','1',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_ELIMINADOS],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','2','1','1',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_ELIMINADOS],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','1',null,'1',VisitaException::AUSENCIA_INFORMACAO_NECESSIDADE_TOUCA],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','1','a','1',VisitaException::AUSENCIA_INFORMACAO_NECESSIDADE_TOUCA],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','1','2','1',VisitaException::AUSENCIA_INFORMACAO_NECESSIDADE_TOUCA],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','1','1',null,VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IMPOSSIVEIS_REMOVER],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','1','1','a',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IMPOSSIVEIS_REMOVER],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','1','1','-1',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IMPOSSIVEIS_REMOVER],
['1','1234567',$s(new \DateTime('today')),null,'logradouro','12345',null,'1','1','1','1','1','2',VisitaException::AUSENCIA_INFORMACAO_CRIADOUROS_IMPOSSIVEIS_REMOVER],
];
      }

      /**
       * @dataProvider visitaProvider
       */
    public function testCriaVisita($id_logradouro,$sinan,$dataref,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover,$msg){

        $this->expectExceptionMessage($msg);
        $visita = new Visita($id_logradouro,$sinan,$dataref,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);
    }

    
}
