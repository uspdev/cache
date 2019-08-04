<?php
require_once '../vendor/autoload.php';
include_once 'pessoa_class.php';

use Uspdev\Cache\Cache;

echo 'Verificando o servidor memcached .. ';
$cache = new Cache();
echo 'ok'.PHP_EOL;