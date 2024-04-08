<?php
declare(strict_types=1);
namespace App\Domain\BoletimGestor;
use \DateTime;
use App\Domain\BoletimGestor\BoletimGestorException;

class BoletimGestor
{
    const CASOS_CONFIRMADOS_MAIOR_SUSPEITOS = "O número de casos confirmados não pode ser maior que o número de casos suspeitos";
    const TESTES_POSITIVOS_MAIOR_REALIZADOS = "O número de testes rápidos positivos não pode ser maior que o número de testes realizados";
    const INCONSISTENCIA_INFORMACAO_ATENDIMENTOS = "Inconsistência na informação de atendimentos versus suspeitos/confirmados";
    const IDENTIFICADOR_BOLETIM_INVALIDO = "O identificador do boletim não é válido !";
    const DATA_INFORMADA_INVALIDA = "A data informada não é válida !";
    const DATA_INFORMADA_MAIOR_HOJE = "A data informada não pode ser maior que hoje !";
    const DATA_INFORMADA_ANTERIOR_PROGRAMA = 'A data informada não pode ser anterior à ' . INICIO_PROGRAMA_FORMAT . ' !';
    const NUMERO_SUSPEITOS_INVALIDO = "O valor informado para total de casos atendidos com suspeita de dengue não é válido";
    const NUMERO_CONFIRMADOS_INVALIDO = "O valor informado para total de casos atendidos confirmados para dengue não é válido";
    const TOTAL_TESTES_RAPIDOS_INVALIDO = "O valor informado para total de testes rapido de dengue realizados não é válido";
    const TOTAL_TESTES_POSITIVOS_INVALIDO = "O valor informado para total de testes rapido de dengue positivos não é válido";
    const ESTOQUE_DIARIO_INVALIDO = "O valor informado para estoque diario dos testes rapidos de dengue não é válido";
    const QDE_ATENDIMENTOS_INVALIDO = "O valor informado para total de atendimentos realizados pela unidade não é válido";
    const CASOS_SUSPEITOS_MAIOR_TOTAL_ATENDIMENTOS = "O número de casos suspeitos não pode ser maior que o total de atendimentos";
    const CASOS_CONFIRMADOS_MAIOR_TOTAL_ATENDIMENTOS = "O número de casos confirmados não pode ser maior que o total de atendimentos";
    const TESTES_REALIZADOS_MAIOR_TOTAL_ATENDIMENTOS = "O número de testes realizados não pode ser maior que o total de atendimentos";
    const TESTES_POSITIVOS_MAIOR_TOTAL_ATENDIMENTOS = "O número de testes positivos não pode ser maior que o total de atendimentos";
    
    
private function verificar_inconsistencias()
    {
        if ($this->total_de_casos_atendidos_confirmados_para_dengue > $this->total_de_casos_atendidos_com_suspeita_de_dengue) {
            throw new BoletimGestorException(self::CASOS_CONFIRMADOS_MAIOR_SUSPEITOS);
            
        }

        if ($this->total_de_testes_rapido_de_dengue_positivos > $this->total_de_testes_rapido_de_dengue_realizados) {
            throw new BoletimGestorException(self::TESTES_POSITIVOS_MAIOR_REALIZADOS);
            
        }

        $dados = [
'total_de_casos_atendidos_com_suspeita_de_dengue'=>self::CASOS_SUSPEITOS_MAIOR_TOTAL_ATENDIMENTOS,
'total_de_casos_atendidos_confirmados_para_dengue'=>self::CASOS_CONFIRMADOS_MAIOR_TOTAL_ATENDIMENTOS,
'total_de_testes_rapido_de_dengue_realizados'=>self::TESTES_REALIZADOS_MAIOR_TOTAL_ATENDIMENTOS,
'total_de_testes_rapido_de_dengue_positivos'=>self::TESTES_POSITIVOS_MAIOR_TOTAL_ATENDIMENTOS,
        ];


        foreach ($dados as $key => $value) {
            if ($this->{$key}> $this->total_de_atendimentos_realizados_pela_unidade) {
                throw new BoletimGestorException($value);
                
            }
        }


    }
    private function __construct(public readonly ?int $id,public readonly DateTime $data_declarada,public readonly int $total_de_casos_atendidos_com_suspeita_de_dengue,public readonly int $total_de_casos_atendidos_confirmados_para_dengue,public readonly int $total_de_testes_rapido_de_dengue_realizados,public readonly int $total_de_testes_rapido_de_dengue_positivos,public readonly int $estoque_diario_dos_testes_rapidos_de_dengue,public readonly int $total_de_atendimentos_realizados_pela_unidade)
    {
        $this->verificar_inconsistencias();
    }


    public static function create(?string $id,?string $data_declarada,?string $total_de_casos_atendidos_com_suspeita_de_dengue,?string $total_de_casos_atendidos_confirmados_para_dengue,?string $total_de_testes_rapido_de_dengue_realizados,?string $total_de_testes_rapido_de_dengue_positivos,?string $estoque_diario_dos_testes_rapidos_de_dengue,?string $total_de_atendimentos_realizados_pela_unidade)
    {
        return new self(self::set_id($id),self::set_data_declarada($data_declarada),self::set_total_de_casos_atendidos_com_suspeita_de_dengue($total_de_casos_atendidos_com_suspeita_de_dengue),self::set_total_de_casos_atendidos_confirmados_para_dengue($total_de_casos_atendidos_confirmados_para_dengue),self::set_total_de_testes_rapido_de_dengue_realizados($total_de_testes_rapido_de_dengue_realizados),self::set_total_de_testes_rapido_de_dengue_positivos($total_de_testes_rapido_de_dengue_positivos),self::set_estoque_diario_dos_testes_rapidos_de_dengue($estoque_diario_dos_testes_rapidos_de_dengue),self::set_total_de_atendimentos_realizados_pela_unidade($total_de_atendimentos_realizados_pela_unidade));
    }


    private static function number_verify(?string $numero):bool
    {

        if (!is_numeric($numero) || !preg_match('/^\d+$/',(string)$numero)) {
            return false;
        }

        return true;

    }

    private static function set_id(?string $id):?int
{
    if ($id === null || $id === '') {
        return null;
    }

    if (self::number_verify((string)$id)===false) {
        throw new BoletimGestorException(self::IDENTIFICADOR_BOLETIM_INVALIDO);
        
    }
    
    return (int)$id;
}
private static function set_data_declarada(?string $data_declarada):DateTime
{
  
    if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/',(string)$data_declarada)) {
        throw new BoletimGestorException(self::DATA_INFORMADA_INVALIDA);
    }

    $hoje = new DateTime('today', $GLOBALS['TZ']);
    $data = new DateTime($data_declarada, $GLOBALS['TZ']);
    
    if ($data > $hoje) {
        throw new BoletimGestorException(self::DATA_INFORMADA_MAIOR_HOJE);
    }

    $inicio_programa = new DateTime($GLOBALS['INICIO_PROGRAMA'], $GLOBALS['TZ']);

    if ($data < $inicio_programa) {
        throw new BoletimGestorException(self::DATA_INFORMADA_ANTERIOR_PROGRAMA);
    }
    
    
    return $data;
}
private static function set_total_de_casos_atendidos_com_suspeita_de_dengue(?string $total_de_casos_atendidos_com_suspeita_de_dengue):int
{
    if (!self::number_verify($total_de_casos_atendidos_com_suspeita_de_dengue)) {
        throw new BoletimGestorException(self::NUMERO_SUSPEITOS_INVALIDO);
        
    }
  return (int)$total_de_casos_atendidos_com_suspeita_de_dengue;
}
private static function set_total_de_casos_atendidos_confirmados_para_dengue(?string $total_de_casos_atendidos_confirmados_para_dengue):int
{
    if (!self::number_verify($total_de_casos_atendidos_confirmados_para_dengue)) {
        throw new BoletimGestorException(self::NUMERO_CONFIRMADOS_INVALIDO);
        
    }
  return (int)$total_de_casos_atendidos_confirmados_para_dengue;
}
private static function set_total_de_testes_rapido_de_dengue_realizados(?string $total_de_testes_rapido_de_dengue_realizados):int
{
    if (!self::number_verify($total_de_testes_rapido_de_dengue_realizados)) {
        throw new BoletimGestorException(self::TOTAL_TESTES_RAPIDOS_INVALIDO);
        
    }
  return (int)$total_de_testes_rapido_de_dengue_realizados;
}
private static function set_total_de_testes_rapido_de_dengue_positivos(?string $total_de_testes_rapido_de_dengue_positivos):int
{
    if (!self::number_verify($total_de_testes_rapido_de_dengue_positivos)) {
        throw new BoletimGestorException(self::TOTAL_TESTES_POSITIVOS_INVALIDO);
        
    }
  return (int)$total_de_testes_rapido_de_dengue_positivos;
}
private static function set_estoque_diario_dos_testes_rapidos_de_dengue(?string $estoque_diario_dos_testes_rapidos_de_dengue):int
{
    if (!self::number_verify($estoque_diario_dos_testes_rapidos_de_dengue)) {
        throw new BoletimGestorException(self::ESTOQUE_DIARIO_INVALIDO);
        
    }
  return (int)$estoque_diario_dos_testes_rapidos_de_dengue;
}
private static function set_total_de_atendimentos_realizados_pela_unidade(?string $total_de_atendimentos_realizados_pela_unidade):int
{
    if (!self::number_verify($total_de_atendimentos_realizados_pela_unidade)) {
        throw new BoletimGestorException(self::QDE_ATENDIMENTOS_INVALIDO);
        
    }
  return (int)$total_de_atendimentos_realizados_pela_unidade;
}
}
