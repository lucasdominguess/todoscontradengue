<?php
declare(strict_types=1);
namespace Tests\Domain\Visita;
use \Exception;
use App\Domain\Visita\Visita;
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../../../config.php');


final class VisitaTest extends TestCase
{
    


public function valueProvider():array
{
    
    return [
        [null],
        ['-1'],
        ['a'],
    ];
}


public function sinanProvider()
{
    return [
        [null],
        ['123456'],
        ['12345678'],
        ['1234a67'],
        ['1'],
    ];
}


    public function testDeveRetornarErroLogradouro()
    {
            $id_logradouro = 'a';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataf =  $dataref->format('Y-m-d');
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = '1';
            $identificado_criadouros = '1';
            $eliminado_criadouros = '1';
            $necessidade_touca = '1';
            $criadouros_impossivel_remover = '1';
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("O valor esperado para o identificador do logradouro não é válido");
        $visita = new Visita($id_logradouro,$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);
    }

    /**
     * @dataProvider valueProvider
     * @param ?string $variable
     */
    public function testDeveRetornarErroDataVisitaValorNulo($variable)
    {
            $id_logradouro = '1';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataf =  'a';
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = '1';
            $identificado_criadouros = '1';
            $eliminado_criadouros = '1';
            $necessidade_touca = '1';
            $criadouros_impossivel_remover = '1';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("A data da visita informada não é válida !");
        $visita = new Visita($id_logradouro,$sinan,(string)$variable,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);
    }

    public function testDeveRetornarErroDataVisitaValorfuturo()
    {
            $id_logradouro = '1';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataref->add(date_interval_create_from_date_string("1 day"));
            $dataf =  $dataref->format('Y-m-d');
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = '1';
            $identificado_criadouros = '1';
            $eliminado_criadouros = '1';
            $necessidade_touca = '1';
            $criadouros_impossivel_remover = '1';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("A data da visita não pode ser maior que hoje !");
        $visita = new Visita($id_logradouro,$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);
    }

    public function testDeveRetornarErroDataVisitaValorPassado()
    {
            $id_logradouro = '1';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataref->add(date_interval_create_from_date_string("1 day"));
            $dataf =  '2023-12-31';
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = '1';
            $identificado_criadouros = '1';
            $eliminado_criadouros = '1';
            $necessidade_touca = '1';
            $criadouros_impossivel_remover = '1';
        
        $this->expectException(Exception::class);


        $this->expectExceptionMessage("A data da visita não pode ser anterior à 01/01/2024 !");
        $visita = new Visita($id_logradouro,$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);
    }

    /**
     * @dataProvider sinanProvider
     * $param ?string $value
     */
   public function testDeveRetornarErroSinanNaoNumerico($value)
    {
            $id_logradouro = '1';
            $sinan = $value;
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataf =  $dataref->format('Y-m-d');
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = '1';
            $identificado_criadouros = '1';
            $eliminado_criadouros = '1';
            $necessidade_touca = '1';
            $criadouros_impossivel_remover = '1';
        
        $this->expectException(Exception::class);
        

        $this->expectExceptionMessage("O número do sinan não é válido !");
        $visita = new Visita($id_logradouro,(string)$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);
    }
   

    public function testDeveRetornarErroImovelVisitado()
    {
            $id_logradouro = '1';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataf =  $dataref->format('Y-m-d');
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = null;
            $imovel_vistoriado = '1';
            $identificado_criadouros = '1';
            $eliminado_criadouros = '1';
            $necessidade_touca = '1';
            $criadouros_impossivel_remover = '1';
        
        $this->expectException(Exception::class);
        

        $this->expectExceptionMessage("Por favor, informe se o imóvel foi visitado !");
        $visita = new Visita($id_logradouro,$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);
    }

    public function testDeveRetornarErroImovelVistoriado()
    {
            $id_logradouro = '1';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataf =  $dataref->format('Y-m-d');
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = null;
            $identificado_criadouros = '1';
            $eliminado_criadouros = '1';
            $necessidade_touca = '1';
            $criadouros_impossivel_remover = '1';
        
        $this->expectException(Exception::class);
        

        $this->expectExceptionMessage("Informe se o imóvel foi vistoriado !");
        $visita = new Visita($id_logradouro,$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);

        
    }
    public function testDeveRetornarErroIdentificadoCriadouros()
    {
            $id_logradouro = '1';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataf =  $dataref->format('Y-m-d');
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = '1';
            $identificado_criadouros = null;
            $eliminado_criadouros = null;
            $necessidade_touca = null;
            $criadouros_impossivel_remover = null;
        
        $this->expectException(Exception::class);
        

        $this->expectExceptionMessage("Informe se foram identificados criadouros !");
        $visita = new Visita($id_logradouro,$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);

        
    }

    public function testDeveRetornarErroEliminadoCriadouros()
    {
            $id_logradouro = '1';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataf =  $dataref->format('Y-m-d');
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = '1';
            $identificado_criadouros = '1';
            $eliminado_criadouros = null;
            $necessidade_touca = null;
            $criadouros_impossivel_remover = null;
        
        $this->expectException(Exception::class);
        

        $this->expectExceptionMessage("Informe se foram eliminados os criadouros !");
        $visita = new Visita($id_logradouro,$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);

        
    }
    public function testDeveRetornarErroPendenciaTouca()
    {
            $id_logradouro = '1';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataf =  $dataref->format('Y-m-d');
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = '1';
            $identificado_criadouros = '1';
            $eliminado_criadouros = '0';
            $necessidade_touca = null;
            $criadouros_impossivel_remover = null;
        
        $this->expectException(Exception::class);
        

        $this->expectExceptionMessage("Informe se há necessidade de touca para caixa d'agua !");
        $visita = new Visita($id_logradouro,$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);

        
    }
    public function testDeveRetornarErroCriadouroImpossivelRemover()
    {
            $id_logradouro = '1';
            $sinan = '1234567';
            $dataref =  new \DateTime('today',$GLOBALS['TZ']);
            $dataf =  $dataref->format('Y-m-d');
            $cep_visita = '1';
            $logradouro = 'cinco caracteres';
            $num_logradouro = '1';
            $complemento_logradouro = '';
            $imovel_visitado = '1';
            $imovel_vistoriado = '1';
            $identificado_criadouros = '1';
            $eliminado_criadouros = '0';
            $necessidade_touca = '0';
            $criadouros_impossivel_remover = null;
        
        $this->expectException(Exception::class);
        

        $this->expectExceptionMessage("Informe se há criadouros impossíveis de remover !");
        $visita = new Visita($id_logradouro,$sinan,$dataf,$cep_visita,$logradouro,$num_logradouro,$complemento_logradouro,$imovel_visitado,$imovel_vistoriado,$identificado_criadouros,$eliminado_criadouros,$necessidade_touca,$criadouros_impossivel_remover);

        
    }


}
