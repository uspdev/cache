# Cache
Biblioteca que cacheia resultados de métodos, geralmente consultas a banco de dados. Esta biblioteca funciona como um conector para o backend memcached. O servidor roda na máquina local.

Pode ser usado em métodos de classes instanciadas e em métodos estáticos.

Os dados ficam guardados no cache em função dos parâmetros passados para o método. Mudando um parâmetro é feito um novo cache.

Por padrão os caches expiram depois de 4 horas ou reiniciando o servidor memcached.

## Requisitos

1. memcached
2. php-memcached

## Instalação e configuração

### Instalação do servidor memcached
* no ubuntu, para instalar o memcached use ```apt install memcached```
* acrescente no ```/etc/memcached.conf``` a linha ```I = 5M ``` para aumentar para 5MB o tamanho de cada objeto ou outro valor que achar conveniente
* reinicie o serviço ``` service memcached restart ```

### Instalação da biblioteca
* Para ubuntu use ``` apt install php-memcached ```
* reinicie o apache ``` service apache2 reload ```
* Coloque a biblioteca como dependencia ``` composer require USPdev/cache ```

## Utilização

Caso típico de uma consulta ao BD:

    $pessoa = new Pessoa();
    $lista = $pessoa->lista();

Usando o cache, a consulta fica assim:

    $pessoa = new Pessoa();
    $cache = new cache($pessoa);
    $lista = $cache->getCached('lista','');

Se o método for estático fica assim:

    $cache = new cache();
    $lista = $cache->getCached('Pessoa::lista','');

## Testes

Rode alguns testes 

    php test/runtests.php
