<?php
require_once dirname(__FILE__) . '/../src/Cache.php';
use Uspdev\Cache\Cache;

echo 'Valores padrão: ';
$cache = new Cache();
echo json_encode($cache->status()), PHP_EOL;
unset($cache);

echo 'Modificando: ';
putenv('USPDEV_CACHE_DISABLE=1');
putenv('USPDEV_CACHE_SMALL=100');
define('USPDEV_CACHE_EXPIRY', 1000);
$cache = new Cache();
echo json_encode($cache->status());
echo PHP_EOL;
