<?php

namespace Alura\Leilao\Services;

use Alura\Leilao\Model\Leilao;

class Avaliador
{
    private $maiorValor = -INF;
    private $menorValor = INF;
    private $maioresLances;

    public function avalia(Leilao $leilao)
    {
        if ($leilao->estaFinalizado()) {
            throw new \DomainException('Leilão já finalizado');
        }

        $lances = $leilao->getLances();

        if ($lances === []) {
            throw new \DomainException('Não é possível avaliar um leilão sem lances');
        }

        foreach ($lances as $lance) {
            if (empty($this->maiorValor) || $lance->getValor() > $this->maiorValor) {
                $this->maiorValor = $lance->getValor();
            }
            if (empty($this->menorValor) || $lance->getValor() < $this->menorValor) {
                $this->menorValor = $lance->getValor();
            }
        }

        usort($lances, function ($lanceUm, $lanceDois) {
            return $lanceDois->getValor() - $lanceUm->getValor();
        });

        $this->maioresLances = array_slice($lances, 0, 3);
    }

    public function getMaiorValor(): float
    {
        return $this->maiorValor;
    }

    public function getMenorValor(): float
    {
        return $this->menorValor;
    }

    public function getMaioresLances()
    {
        return $this->maioresLances;
    }
}
