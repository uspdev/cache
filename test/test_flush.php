<?php
require_once dirname(__FILE__) . '/../src/Cache.php';
use Uspdev\Cache\Cache;

echo 'Limpeza do conteúdo do cache: ';
putenv('USPDEV_CACHE_DISABLE');
putenv('USPDEV_CACHE_SMALL');
putenv('USPDEV_CACHE_EXPIRY');
$cache = new Cache();
echo json_encode($cache->status()), PHP_EOL;
$cache->flush();
sleep(1);
echo json_encode($cache->status()), PHP_EOL;

echo 'OK', PHP_EOL;


