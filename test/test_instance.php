<?php
require_once dirname(__FILE__).'/../src/Cache.php';
include_once 'pessoa_class.php';

use Uspdev\Cache\Cache;

echo 'Verificando o servidor memcached .. ';
$cache = new Cache();
echo 'ok'.PHP_EOL;