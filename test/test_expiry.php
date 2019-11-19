<?php
require_once dirname(__FILE__) . '/../src/Cache.php';
include_once 'pessoa_class.php';

use Uspdev\Cache\Cache;

$cache = new Cache();
echo 'Setando expiração para 1s'.PHP_EOL;
$cache->expiry = 1;

echo 'Chamando com parametro aleatório: ';
$start_time = microtime(true);
$rand = rand(0, 100);
$lista = $cache->getCached('pessoa::lista', $rand);
$elapsed = microtime(true) - $start_time;
echo 'demorou ' . number_format($elapsed, 5) . ' segundos' . PHP_EOL;

echo 'Chamando com o mesmo parametro aleatório: ';
$start_time = microtime(true);
$lista = $cache->getCached('pessoa::lista', $rand);
$elapsed = microtime(true) - $start_time;
echo 'demorou ' . number_format($elapsed, 5) . ' segundos' . PHP_EOL;

echo 'Aguardando 2s para chamar novamente e ver expirado'.PHP_EOL;
sleep(2);

echo 'Chamando com o mesmo parametro aleatório: ';
$start_time = microtime(true);
$lista = $cache->getCached('pessoa::lista', $rand);
$elapsed = microtime(true) - $start_time;
echo 'demorou ' . number_format($elapsed, 5) . ' segundos' . PHP_EOL;
