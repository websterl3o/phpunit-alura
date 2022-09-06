<?php

namespace Tests\Models;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;

class LeilaoTest extends TestCase
{
    public function geraLances(): array
    {
        $maria   = new Usuario('Maria');
        $joao    = new Usuario('João');
        $joaquim = new Usuario('Joaquim');

        $leilaoCom1Lance = new Leilao('Focus 2007 0KM');
        $leilaoCom1Lance->recebeLance(new Lance($joao, 12200));

        $leilaoCom2Lances = new Leilao('Fiat 147 0KM');
        $leilaoCom2Lances->recebeLance(new Lance($joao, 2000));
        $leilaoCom2Lances->recebeLance(new Lance($maria, 2500));

        $leilaoCom3Lances = new Leilao('Uno com escada 0KM');
        $leilaoCom3Lances->recebeLance(new Lance($joao, 2000));
        $leilaoCom3Lances->recebeLance(new Lance($maria, 2500));
        $leilaoCom3Lances->recebeLance(new Lance($joaquim, 3000));

        return [
            'leilaoCom1Lance'  => [1, $leilaoCom1Lance, [12200]],
            'leilaoCom2Lances' => [2, $leilaoCom2Lances, [2000, 2500]],
            'leilaoCom3Lances' => [3, $leilaoCom3Lances, [2000, 2500, 3000]],
        ];
    }

    /**
     * @dataProvider geraLances
     */
    public function testLeilaoDeveReceberLances(int $quantidadeLances, Leilao $leilao, array $valorLances): void
    {
        self::assertCount($quantidadeLances, $leilao->getLances());

        foreach ($valorLances as $i => $valorLance) {
            self::assertEquals($valorLance, $leilao->getLances()[$i]->getValor());
        }
    }

    public function testLeilaoNaoDeveReceberLancesSeguidos()
    {
        $leilao = new Leilao('Corsa 0KM');
        $joao   = new Usuario('João');

        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($joao, 3000));

        self::assertCount(1, $leilao->getLances());
        self::assertEquals(2000, $leilao->getLances()[0]->getValor());
    }

    public function testLeilaoNaoDeveAceitarMaisDe5LancesPorUsuario()
    {
        $leilao = new Leilao('Fusca 0KM');
        $joao   = new Usuario('João');
        $maria  = new Usuario('Maria');

        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joao, 3000));
        $leilao->recebeLance(new Lance($maria, 3500));
        $leilao->recebeLance(new Lance($joao, 4000));
        $leilao->recebeLance(new Lance($maria, 4500));
        $leilao->recebeLance(new Lance($joao, 5000));
        $leilao->recebeLance(new Lance($maria, 5500));
        $leilao->recebeLance(new Lance($joao, 6000));
        $leilao->recebeLance(new Lance($maria, 6500));

        $leilao->recebeLance(new Lance($joao, 7000));
        $leilao->recebeLance(new Lance($maria, 7500));

        self::assertCount(10, $leilao->getLances());
        self::assertEquals(6500, $leilao->getLances()[count($leilao->getLances()) - 1]->getValor());
    }
}
