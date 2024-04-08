<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Sinan;

use App\Infrastructure\Persistence\Sql\Sql;

final class SinanRepository
{
    function __construct(private Sql $sql)
    {

    }


    public function cadastrarEncerramentoSinan(int $id_user,string $sinan, string $data_fim_bloqueio,int $todos_quarteiroes_visitados):array
    {

        $this->sql->beginTransaction();

        $stmt = $this->sql->prepare('select count(*) as total from logradouros_para_visitar where id_user = :id_user and sinan = :sinan and data_fim_bloqueio is null and ativo = 1');
        $dados = [':id_user'=>$id_user,':sinan'=>$sinan];
        $this->sql->setParams($stmt, $dados);

        $stmt->execute();
        $counter = $stmt->fetch($this->sql::FETCH_ASSOC);
        $counter = (int)$counter['total'];

        if ($counter === 0) {
            $this->sql->rollBack();
            return ['cod'=>'fail','message'=>'O sinan informado já foi encerrado ou não existe'];

        }

        $stmt = $this->sql->prepare("update logradouros_para_visitar set todos_quarteiroes_visitados = :todos_quarteiroes_visitados, data_fim_bloqueio = to_date(:data_fim_bloqueio,'YYYY-MM-DD') WHERE sinan = :sinan and id_user = :id_user and data_fim_bloqueio is null and ativo = 1");
        $dados = [':id_user'=>$id_user,':sinan'=>$sinan,':data_fim_bloqueio'=>$data_fim_bloqueio,':todos_quarteiroes_visitados'=>$todos_quarteiroes_visitados];

        $this->sql->setParams($stmt, $dados);

        try {
            $stmt->execute();
            $this->sql->commit();
            return ['cod'=>'ok','message'=>'Sinan encerrado com sucesso'];
        } catch (\Throwable $th) {
            return ['cod'=>'fail','message'=>'Não foi possível encerrar o sinan'];
        }

    }


    public function desativa_sinan(string $sinan, int $id_user)
    {
        $stmt = $this->sql->prepare('update logradouros_para_visitar set ativo = 0 where sinan = :sinan and id_user = :id_user');
        $dados = [':sinan'=>$sinan,':id_user'=>$id_user];
        $this->sql->setParams($stmt, $dados);

        try {
            $stmt->execute();
            return ['cod'=>'ok','message'=>'Atualizado com sucesso'];
        } catch (\Throwable $th) {
            return ['cod'=>'fail','message'=>'Não foi possível desativar o sinan sollicitado'];
        }
}
}
