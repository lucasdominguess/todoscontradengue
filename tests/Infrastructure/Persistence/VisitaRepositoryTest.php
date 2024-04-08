<?php
declare(strict_types=1);
namespace Tests\Infrastructure\Persistence;
use App\Domain\Visita\Visita;
use Tests\TestCase;
use App\Infrastructure\Persistence\Sql\Sql;
use App\Infrastructure\Persistence\Visita\VisitaRepository;

class VisitaRepositoryTest extends TestCase
{
    public VisitaRepository $visitaRepository;
    private array $data;

    public function setUp():void
    {
        $mock = $this->createMock(VisitaRepository::class);
        $mock->method('cadastrar')->willReturn([1,2,3]);
        $this->visitaRepository = $mock;
        

    }


    public function testFake()
    {
        $visita = $this->createMock(Visita::class);
        $this->assertSame([1,2,3],$this->visitaRepository->cadastrar(1,$visita));
    }

}
