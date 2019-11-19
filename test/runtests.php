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
define('USPDEV_CACHE_DISABLE', true);
include 'test_static.php';
