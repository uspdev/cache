<?php
Echo 'Estensão memcached do php .. ';
if (class_exists('memcached')) {
    echo 'ok' . PHP_EOL;
} else {
    echo 'faltando memcached' . PHP_EOL;
    echo 'veja o readme para instalar e configurar o memcached'.PHP_EOL;
    exit;
}
