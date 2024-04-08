<?php
declare(strict_types=1);
namespace Tests\Infrastructure\Persistence;
use Tests\TestCase;
use App\Domain\User\User;
use App\Domain\BoletimGestor\BoletimGestor;
use App\Infrastructure\Persistence\Sql\Sql;
use App\Infrastructure\Persistence\BoletimGestor\BoletimGestorRepository;


class BoletimGestorRepositoryTest extends TestCase
{
    private BoletimGestorRepository $boletimGestorRepository;
    private Sql $sql;


    protected function setUp():void
    {
        $this->sql = new Sql();
        $this->boletimGestorRepository = new BoletimGestorRepository($this->sql);
    }


    /**
     * @test
     */
    public function salvamentoUnicoDiario()
    {
        @session_start();
        $_SESSION[User::USER_CNES] = '';
        $_SESSION[User::USER_ID] = '906';

        $s = fn(\DateTime $data)=>$data->format('Y-m-d');

        $boletimGestor = BoletimGestor::create(null,$s(new \DateTime('today')),'1','1','1','1','1','1');
        $this->sql->query('truncate table boletim_gestor restart identity');
        $res = $this->boletimGestorRepository->salvar($boletimGestor);
        $this->assertArrayHasKey('cod',$res);
        $this->assertSame('ok',$res['cod']);


    }


    /**
     * @test
     */
    public function salvamentoDuplicado()
    {
        @session_start();
        $_SESSION[User::USER_CNES] = '';
        $_SESSION[User::USER_ID] = '906';

        $s = fn(\DateTime $data)=>$data->format('Y-m-d');

        $boletimGestor = BoletimGestor::create(null,$s(new \DateTime('today')),'1','1','1','1','1','1');
        $this->sql->query('truncate table boletim_gestor restart identity');
        $res = $this->boletimGestorRepository->salvar($boletimGestor);
        $res = $this->boletimGestorRepository->salvar($boletimGestor);
        $this->assertArrayHasKey('cod',$res);
        $this->assertSame('fail',$res['cod']);
        $this->assertSame('Um único lançamento por data é permitido.',$res['msg']);
        
    }
}
