<?php
require_once '../vendor/autoload.php';
include_once 'pessoa_class.php';

use Uspdev\Cache\Cache;

echo 'Jeito normal de chamar método estático: ';
$start_time = microtime(true);
$lista = pessoa::lista();
$elapsed = microtime(true) - $start_time;
echo 'demorou '. $elapsed . ' segundos'.PHP_EOL;

$cache = new Cache();

echo 'Chamando com cache: ' ;
$start_time = microtime(true);
$lista = $cache->getCached('pessoa::lista', '');
$elapsed = microtime(true) - $start_time;
echo 'demorou '.$elapsed . ' segundos'.PHP_EOL;

echo 'Chamando com cache novamente: ' ;
$start_time = microtime(true);
$lista = $cache->getCached('pessoa::lista', '');
$elapsed = microtime(true) - $start_time;
echo 'demorou '.$elapsed . ' segundos'.PHP_EOL;

echo 'Chamando com parametro aleatório: ' ;
$start_time = microtime(true);
$rand = rand(0,100);
$lista = $cache->getCached('pessoa::lista', $rand);
$elapsed = microtime(true) - $start_time;
echo 'demorou '.$elapsed . ' segundos'.PHP_EOL;

echo 'Chamando com o mesmo parametro aleatório: ' ;
$start_time = microtime(true);
$lista = $cache->getCached('pessoa::lista', $rand);
$elapsed = microtime(true) - $start_time;
echo 'demorou '.$elapsed . ' segundos'.PHP_EOL;

