<?php

namespace Tests\Services;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Services\Avaliador;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    private Avaliador $leiloeiro;

    public function leilaoEmOrdemCrescente(): Leilao
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $maria   = new Usuario('Maria');
        $joao    = new Usuario('João');
        $joaquim = new Usuario('Joaquim');
        $carlos  = new Usuario('Carlos');

        $leilao->recebeLance(new Lance($joao, 2500));
        $leilao->recebeLance(new Lance($joaquim, 3000));
        $leilao->recebeLance(new Lance($carlos, 3500));
        $leilao->recebeLance(new Lance($maria, 20000));

        return $leilao;
    }

    public function leilaoEmOrdemDecrescente(): Leilao
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $maria   = new Usuario('Maria');
        $joao    = new Usuario('João');
        $joaquim = new Usuario('Joaquim');
        $carlos  = new Usuario('Carlos');

        $leilao->recebeLance(new Lance($maria, 20000));
        $leilao->recebeLance(new Lance($carlos, 3500));
        $leilao->recebeLance(new Lance($joaquim, 3000));
        $leilao->recebeLance(new Lance($joao, 2500));

        return $leilao;
    }

    public function leilaoEmOrdemAleatoria(): Leilao
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $maria   = new Usuario('Maria');
        $joao    = new Usuario('João');
        $joaquim = new Usuario('Joaquim');
        $carlos  = new Usuario('Carlos');

        $leilao->recebeLance(new Lance($carlos, 3500));
        $leilao->recebeLance(new Lance($joao, 2500));
        $leilao->recebeLance(new Lance($maria, 20000));
        $leilao->recebeLance(new Lance($joaquim, 3000));

        return $leilao;
    }

    public function entregaLeiloes(): array
    {
        return [
            'ordem-crescente'   => [$this->leilaoEmOrdemCrescente()],
            'ordem-decrescente' => [$this->leilaoEmOrdemDecrescente()],
            'ordem-aleatoria'   => [$this->leilaoEmOrdemAleatoria()],
        ];
    }

    protected function setUp(): void
    {
        $this->leiloeiro = new Avaliador();
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testDeveEncontrarMaiorValor(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maiorValor = $this->leiloeiro->getMaiorValor();

        $valorEsperado = 20000;

        self::assertEquals($valorEsperado, $maiorValor);
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testDeveEncontrarMenorValor(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $menorValor = $this->leiloeiro->getMenorValor();

        $valorEsperado = 2500;

        self::assertEquals($valorEsperado, $menorValor);
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveBuscar3MaioresValores(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maioresLances = $this->leiloeiro->getMaioresLances();

        $quantidadeEsperada = 3;

        self::assertCount($quantidadeEsperada, $maioresLances);
        self::assertEquals(20000, $maioresLances[0]->getValor());
        self::assertEquals(3500, $maioresLances[1]->getValor());
        self::assertEquals(3000, $maioresLances[2]->getValor());
    }

    public function testNaoAvaliaLeilaoSemLances()
    {
        $leilao = new Leilao('Fusca 0KM');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é possível avaliar um leilão sem lances');

        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoFinalizadoNaoPodeAvaliar()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leilão já finalizado');

        $leilao = new Leilao('Fusca 0KM');

        $joao = new Usuario('João');

        $leilao->recebeLance(new Lance($joao, 2000));

        $leilao->finaliza();

        $this->leiloeiro->avalia($leilao);
    }
}
