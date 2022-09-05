<?php

require 'vendor/autoload.php';

$leilao = new \Alura\Leilao\Model\Leilao('Fiat 147 0KM');

$maria = new \Alura\Leilao\Model\Usuario('Maria');
$joao = new \Alura\Leilao\Model\Usuario('João');
$joaquim = new \Alura\Leilao\Model\Usuario('Joaquim');
$carlos = new \Alura\Leilao\Model\Usuario('Carlos');

$leilao->recebeLance(new \Alura\Leilao\Model\Lance($maria, 20000));
$leilao->recebeLance(new \Alura\Leilao\Model\Lance($joao, 2500));
$leilao->recebeLance(new \Alura\Leilao\Model\Lance($joaquim, 3000));
$leilao->recebeLance(new \Alura\Leilao\Model\Lance($carlos, 3500));

$leiloeiro = new \Alura\Leilao\Services\Avaliador();
$leiloeiro->avalia($leilao);

$maiorValor = $leiloeiro->getMaiorValor();

echo "O maior lance foi de R$ {$maiorValor}".PHP_EOL;
