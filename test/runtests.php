<?php
echo 'test_extension' . PHP_EOL;
include 'test_extension.php';

echo PHP_EOL . 'test_instance' . PHP_EOL;
include 'test_instance.php';

echo PHP_EOL . 'test_class' . PHP_EOL;
include 'test_class.php';

echo PHP_EOL . 'test_static' . PHP_EOL;
include 'test_static.php';

echo PHP_EOL . 'test_static_multiple' . PHP_EOL;
include 'test_multiple_param.php';

echo PHP_EOL . 'test_static_multiple modificando expiracao' . PHP_EOL;
include 'test_expiry.php';

echo PHP_EOL . 'test static com cache desabilitado' . PHP_EOL;
putenv('USPDEV_CACHE_DISABLE=1');
include 'test_static.php';

echo PHP_EOL . 'test static definindo USPDEV_CACHE_SMALL = 1K' . PHP_EOL;
putenv('USPDEV_CACHE_DISABLE=0');
define('USPDEV_CACHE_SMALL', 1024);
include 'test_static.php';

echo PHP_EOL . 'test status' . PHP_EOL;
include 'test_status.php';

echo PHP_EOL . 'test flush' . PHP_EOL;
include 'test_flush.php';